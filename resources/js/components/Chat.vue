<template>
  <div class="row">
    <div class="alert alert-success col-6 offset-3" v-if="notify">
      {{notify}}
    </div>
    <div class="col-8">
      <div class="card card-default">
        <div class="card-header">Message</div>
        <div class="card-body p-0">
          <ul class="list-unstyled" style="height: 300px; overflow-y: scroll" v-chat-scroll="{smooth: true, notSmoothOnInit: true}">
            <li class="p-2" v-for="(message , index) in messages" :key="index">
              <strong>{{message.user.name}}</strong>
              {{message.message}}
            </li>
          </ul>
        </div>
        <input

          @keydown="listenToTypingUser"
          @keyup.enter ="sendMessage"
          v-model="newMessage"
          type="text"
          class="form-control"
          name="message"
          placeholder="Enter message"
        />
      </div>
      <span class="text-muted" v-if="activeUser">{{activeUser.name}} is typing ...</span>
    </div>
    <div class="col-4">
      <div class="card card-default">
        <div class="card-header">Active Users</div>
        <div class="card-body">
          <ul>
            <li class="py-2" v-for="(user , index) in users" :key="index">{{user.name}}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props:['user'],

    data() {
        return {
        messages:[],
        newMessage:'',
        users:[],
        notify: '',
        activeUser:false,
        typingTimer: false
        }
    },

    created(){
        this.fetchMessages();
        this.listenMessage();
         
    },

    methods:{
        fetchMessages(){
            axios.get('/get-message').then((res)=>{
              this.messages = res.data
            })
        },

        sendMessage(){
          axios.post('send-message',{message : this.newMessage})
            .then(res => {
              this.messages.push({
                message: this.newMessage,
                user: this.user
              })
              this.newMessage = ''
            })
        },

        listenMessage(){
         Echo.join('ChatChannel')
         .here(user =>{
           this.users = user;
         })
         .joining(user => {
           this.users.push(user);
         })
         .leaving(user =>{
           this.users = this.users.filter(usr => usr.id != user.id);
         })
         
         .listen('ChatEvent',(event) => {
           this.notify = 'You have new message man from '+this.user.name
           setInterval(()=> {
             this.notify = ''
           }, 3000)
           this.messages.push(
             event.message
           )
         })
         .listenForWhisper('typing' , name => {
           this.activeUser = name;

           if(this.typingTimer) {
             clearTimeout(this.typingTimer)
           }
           this.typingTimer = setTimeout(() => {
             this.activeUser = false
           } , 3000)
         })
         
        },

        listenToTypingUser(){
          Echo.join('ChatChannel')
           .whisper('typing', {
            name: this.user.name
        });
        }
    }
};
</script>