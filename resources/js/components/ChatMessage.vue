<template>
    <div>
        <div v-if="!firebaseMessagesLoaded" class="ui active centered inline text loader">Loading conversation ...</div>
        <div id="comments-container" style="height:50vh;overflow-y: scroll;padding-right:10px;padding-top:10px;padding-bottom: 10px;" >
            <div class="msg" v-if="historyMessages.length > 0" v-for="message in historyMessages" v-cloak>
               <!-- <p v-html="message.text">{{ message.text }}</p> -->
                <div v-if="!isMe(message.userId)" class="sixteen wide column">
                    <div class="text from">
                        <div class="avatar">
                          <img class="direct-chat-img" :src='getReceptorImage()'>
                        </div> 
                        <p v-html="message.text" v-if="message.text">{{ message.text }}</p>
                        <div v-if="message.mediaType === 'Image'">
                            <a :href="getMediaImage(message.image)" class="timeline_fancybox" :data-fancybox="message.userId" title="Click To View">
                                <img :src="getMediaImage(message.image)" height="125" width="125">
                            </a>
                        </div>
                        <div v-if="message.mediaType === 'Video'">
                            <a :href="getMediaImage(message.video)" class="timeline_fancybox" :data-fancybox="message.userId" title="Click To View">
                                <img :src="getMediaImage(message.image)" height="125" width="125">
                            </a>
                        </div>
                    </div>
                    <div>
                        <span class="date">{{ humanize(message.timestamp) }}</span>
                        <!-- <span>
                            <a class="author">{{ getUserName(message.userId) }}</a>
                        </span> -->
                    </div>
                </div>

                <div v-else class="sixteen wide column">
                    <div class="text to">
                        <p v-html="message.text" v-if="message.text">{{ message.text }}</p>
                        <div v-if="message.mediaType === 'Image'">
                            <a :href="getMediaImage(message.image)" class="timeline_fancybox" :data-fancybox="message.userId" title="Click To View">
                                <img :src="getMediaImage(message.image)" height="125" controls>
                            </a>
                        </div>
                        <div v-if="message.mediaType === 'Video'">
                            <a :href="getMediaImage(message.video)" class="timeline_fancybox videoBox" :data-fancybox="message.userId" title="Click To View">
                                <img :src="getMediaImage(message.image)" height="125" width="125" >
                            </a>
                        </div>
                        <div class="avatar">
                            <img class="direct-chat-img" :src='getUserImage()'>
                        </div>
                    </div>
                    <div>
                        <span class="date text-right">{{ humanize(message.timestamp) }}</span>
                        <!-- <span>
                            <a class="author">{{ getUserName(message.userId) }}</a>
                        </span> -->
                    </div>
                </div>
            </div>
        </div>

        <div v-if="firebaseMessagesLoaded && historyMessages.length < 1">
            <p>No Messages, send the first message to start the conversation.</p>
        </div>
        <div class="field">
            <twemoji-textarea
                :emojiData="emojiDataAll"
                :emojiGroups="emojiGroups"
                @enterKey="onEnterKey">
            </twemoji-textarea>
        </div>
        <form @submit.prevent="sendMessage($event)" class="ui reply form" enctype="multipart/form-data">
            <!--<div class="field">
                <textarea class="form-control" id="twemoji-textarea" placeholder="Write a message."  v-model="message.text" />
            </div>-->
            <div class="field emoji-page imgUpload">
                <input type="file" class="form-control" id="poster" placeholder="Upload Image" v-on:change="onImageChange" >
            </div>
            <div v-if="!loading" class="submit_btn" >
                <button type="submit" class="ui blue labeled submit icon button" style="float:right;">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 492 492" style="enable-background:new 0 0 492 492;" xml:space="preserve">
                    <g>
                        <g>
                            <path d="M442.627,185.388L265.083,7.844C260.019,2.78,253.263,0,245.915,0c-7.204,0-13.956,2.78-19.02,7.844L49.347,185.388
                                c-10.488,10.492-10.488,27.568,0,38.052l16.12,16.128c5.064,5.06,11.82,7.844,19.028,7.844c7.204,0,14.192-2.784,19.252-7.844
                                l103.808-103.584v329.084c0,14.832,11.616,26.932,26.448,26.932h22.8c14.832,0,27.624-12.1,27.624-26.932V134.816l104.396,104.752
                                c5.06,5.06,11.636,7.844,18.844,7.844s13.864-2.784,18.932-7.844l16.072-16.128C453.163,212.952,453.123,195.88,442.627,185.388z"
                                />
                        </g>
                    </g>
                    </svg>
                </button>
            </div>
            <div v-if="loading"></div>
        </form>
        
    </div>
</template>

<script>
    
    import { TwemojiTextarea } from '@kevinfaguiar/vue-twemoji-picker';
    import EmojiAllData from '@kevinfaguiar/vue-twemoji-picker/emoji-data/en/emoji-all-groups.json';
    import EmojiGroups from '@kevinfaguiar/vue-twemoji-picker/emoji-data/emoji-groups.json';
    // const mailRegex = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    const mailRegex = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;
    // const mobileNoRegex = /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/;
    const mobileNoRegex = /(?:(?:\+?([1-9]|[0-9][0-9]|[0-9][0-9][0-9])\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([0-9][1-9]|[0-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?/;
    export default {
        props: ['userId', 'chatId', 'receptorName', 'receptorImage', 'userImage', 'receptorId', 'userName' ,'projectapiUrl','projectapiUrlMedia'],
        components: { 'twemoji-textarea': TwemojiTextarea },
        data() {
            return {
                message: {
                    text: '',
                    date: null
                },
                historyMessages: [],
                firstLoad: false,
                firebaseMessagesLoaded: false,
                loading: false,
            }
        },
        computed: {
            emojiDataAll() {
                return EmojiAllData;
            },
            emojiGroups() {
                return EmojiGroups;
            }
        },
        mounted(){
            database.ref('/chats/' + this.chatId)
                .on('value', snapshot => this.loadMessages(snapshot.val()))
        },
        methods:{
            onEnterKey(e) {
                console.log("ClickedEnter", e);
            },
            loadMessages(messages){
                this.firebaseMessagesLoaded= false;
                this.historyMessages = [];
                for(let key in messages) {
                    this.historyMessages.push({
                        userId: messages[key].fromUserId,
                        text: messages[key].text,
                        image: messages[key].imageURL,
                        video: messages[key].videoURL,
                        timestamp: messages[key].timestamp,
                        mediaType: messages[key].mediaType,
                        userChatID:key,
                        chatID:this.chatId,
                    });
                }
                this.showNotification(this.historyMessages.slice(-1).pop());
                this.firstLoad = true;
                //scroll to bottom
                var window_height = $(window).height();
                var document_height = $(document).height();
                $('#comments-container').animate({ scrollTop: window_height + document_height + (1000 * 1000) });
                this.firebaseMessagesLoaded= true;
            },
            onImageChange(e){
                this.image = e.target.files[0];
            },
             sendMessage(e){
                this.loading = true;
                var uid = this.userId;
                // Get a key for a new Post.
                var newPostKey = database.ref('/chats/' + this.chatId).push().key;
                var imagesRef = firebase.storage().ref().child('images');
                var file = $('#poster').get(0).files[0];
                var msg = $('#twemoji-textarea').html();
                if (!mailRegex.test(msg)){
                    if (!mobileNoRegex.test(msg)){
                        if(file) {
                            var mimes = {
                                "image/gif": {
                                    "source": "iana",
                                    "compressible": false,
                                    "extensions": ["gif"]
                                },
                                "image/jpeg": {
                                    "source": "iana",
                                    "compressible": false,
                                    "extensions": ["jpeg","jpg","jpe"]
                                },
                                "image/png": {
                                    "source": "iana",
                                    "compressible": false,
                                    "extensions": ["png"]
                                },
                                "image/svg+xml": {
                                    "source": "iana",
                                    "compressible": true,
                                    "extensions": ["svg","svgz"]
                                },
                                "image/webp": {
                                    "source": "apache",
                                    "extensions": ["webp"]
                                },
                                // "video/mp4": {
                                //     "source": "iana",
                                //     "extensions": ["mp4"]
                                // },
                                // "video/MPV": {
                                //     "source": "iana",
                                //     "extensions": ["MPV"]
                                // },
                                // "video/3gpp": {
                                //     "source": "iana",
                                //     "extensions": ["3gpp"]
                                // },
                            };
                            // Create the file metadata
                            var metadata = {
                                contentType: file.type
                            };
                            if(mimes[file.type]) {
                                if (file.size > 1024 * 1024 * 5) {
                                    alert('You cannot add a file that has sizeof more than 5MB');
                                }else{
                                    if(file.type == 'video/mp4' || file.type == 'video/MPV' || file.type == 'video/3gpp') {
                                        // Upload video file and metadata to the object
                                        const formData = new FormData()
                                        formData.append('mediaFile', file)
                                        formData.append('mediaType', "Video")
                                        axios.post(this.projectapiUrlMedia, formData)
                                        .then(response => {
                                            database.ref('/chats/' + this.chatId).push({
                                                fromUserId: this.userId,
                                                text: msg,
                                                imageURL: response.data.data.thumbnailPath,
                                                videoURL: response.data.data.mediaPath,
                                                mediaType: 'Video',
                                                name: this.receptorName,
                                                timestamp: moment().toDate().getTime()
                                            })
                                            .then(() => {
                                                $('#twemoji-textarea').html('');
                                                $('#poster').val('');
                                                axios.post(this.projectapiUrl, {
                                                    chatId: this.chatId,
                                                    textmessage: 'Document',
                                                    userId: this.userId
                                                })
                                                .then(function (response) {
                                                    console.log('success');
                                                })
                                                .catch(function (error) {
                                                    console.log(error);
                                                });
                                            });
                                        })
                                    } else {
                                        // Upload image file and metadata to the object
                                        const formData = new FormData()
                                        formData.append('mediaFile', file)
                                        formData.append('mediaType', "Image")
                                        axios.post(this.projectapiUrlMedia, formData)
                                        .then(response => {
                                            database.ref('/chats/' + this.chatId).push({
                                                fromUserId: this.userId,
                                                text: msg,
                                                imageURL: response.data.data.mediaPath,
                                                videoURL: '',
                                                mediaType: 'Image',
                                                name: this.receptorName,
                                                timestamp: moment().toDate().getTime()
                                            })
                                            .then(() => {
                                                $('#twemoji-textarea').html('');
                                                $('#poster').val('');
                                                axios.post(this.projectapiUrl, {
                                                    chatId: this.chatId,
                                                    textmessage: 'Document',
                                                    userId: this.userId
                                                })
                                                .then(function (response) {
                                                    console.log('success');
                                                })
                                                .catch(function (error) {
                                                    console.log(error);
                                                });
                                            });
                                        })
                                        .catch(function (error) {
                                            console.log(error);
                                        });
                                    }
                                }
                            } else {
                                alert('Please select proper file format. Please upload image only.');
                            }
                        } else if(msg) {
                            database.ref('/chats/' + this.chatId).push({
                                fromUserId: this.userId,
                                text: msg,
                                imageURL: '',
                                videoURL: '',
                                mediaType: 'Text',
                                name: this.receptorName,
                                timestamp: moment().toDate().getTime()
                            })
                            .then(() => {
                                $('#twemoji-textarea').html('');
                                axios.post(this.projectapiUrl, {
                                    chatId: this.chatId,
                                    textmessage: msg,
                                    userId: this.userId
                                })
                                .then(function (response) {
                                    console.log('success');
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                            });
                        } else {
                            alert('Please select image or Type message');
                        }
                    }else{
                        alert('Please do not share your mobile number');    
                    }
                }else{
                    alert('Please do not share your mail address');
                }
                this.loading = false;
            },
            getUserName(id){
                if(id == this.userId) {
                    return this.userName;
                }else {
                    return this.receptorName
                }
            },
            getUserImage(id){
                return this.userImage
            },
            getReceptorImage(){
                return this.receptorImage
            },
            isMe(id) {
                if(id == this.userId) {
                    return true;
                }else {
                    return false;
                }
            },

            humanize(date) {
                return moment(date).format('DD-MM-YY h:mma');
            },
            getMediaImage(id){
                return process.env.MIX_APP_URL+'/resources/uploads/chat_image/'+id;
            },
            showNotification(message){
                if(this.firstLoad && message.userId != this.message.userId && !windowFocus) {
                    pushjs.create(this.getUserName(message.userId), {
                        body: message.text,
                        timeout: 4000,
                        onClick: function () {
                            // console.log('asdasd');
                            window.focus();
                            this.close();
                        }
                    });
                }
            },
        }

        // For delete chat 
        // database.ref('/chats/20').remove().then(function() {
        // })
    }
</script>
