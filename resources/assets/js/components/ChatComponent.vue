<template>
    <div class="card">
        <div class="card-header">{{ otherUser.first_name }}</div>

        <div class="card-body">
            <div v-for="message in messages" v-bind:key="message.id">
                <div v-if="message.author === authUser.email" class="sixteen wide column">
                    <div class="comment" style="text-align: right;">
                        <a class="avatar" style="float:right;">
                            <img class="direct-chat-img" :src="getAuthUserImage()">
                        </a>
                        <div class="content" style="margin-left:0;margin-right: 3.5em">
                            <div class="metadata metadata-author">
                                <div>
                                    <span class="date" style="float: left;">{{ humanize(message.created_at) }}</span>
                                    <span>
                                        <a class="author">{{authUser.first_name}}</a>
                                    </span>
                                </div>
                                <div class="text">
                                    <div v-if="message.type === 'media'">
                                        <img class="img-thumbnail" :src="message.mediaUrl" :alt="message.filename" width="150px">
                                    </div>
                                    <div v-else>
                                        <div>
                                            {{ message.body }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="sixteen wide column">
                    <div class="comment">
                        <a class="avatar">
                            <img class="direct-chat-img" :src="getReceptorImage()">
                        </a>
                        <div class="content" style="margin-left: 3.5em">
                            <div class="metadata metadata-user">
                                <div>
                                <span class="date" style="float: right;">{{ humanize(message.created_at) }}</span>
                                    <span>
                                        <a class="author">{{otherUser.first_name}}</a>
                                    </span>
                                </div>
                                <div class="text">
                                    <div v-if="message.type === 'media'">
                                        <img class="img-thumbnail" :src="message.mediaUrl" :alt="message.filename" width="150px">
                                    </div>
                                    <div v-else>
                                        <div >
                                            {{ message.body }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--
            <div v-for="message in messages" v-bind:key="message.id">
                <div :class="{ 'text-right': message.author === authUser.email }">
                    {{ message.body }}
                </div>
            </div>-->
        </div>

        <div class="card-footer">
            <input
                type="text"
                v-model="newMessage"
                class="form-control"
                placeholder="Type your message..."
                @keyup.enter="sendMessage"
            />
        </div>
        <div class="form-group col-md-4">
            <input type="file" accept="image/*" @change="sendMediaMessage">
        </div>
    </div>
</template>
<script>
export default {
    name: "ChatComponent",
    props: {
        authUser: {
            type: Object,
            required: true
        },
        otherUser: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            messages: [],
            newMessage: "",
            channel: "",
        };
    },
    async created() {
        const token = await this.fetchToken();
        await this.initializeClient(token);
        await this.fetchMessages();
    },
    methods: {
        async fetchToken() {
            const { data } = await axios.post(this.otherUser.siteURL+"/api/token", {
                email: this.authUser.email
            });
            return data.token;
        },
        async initializeClient(token) {
            const client = await Twilio.Chat.Client.create(token);

            client.on("tokenAboutToExpire", async () => {
                const token = await this.fetchToken();

                client.updateToken(token);
            });
            // this.channel = await client.getChannelByUniqueName(
            //     `${this.otherUser.id}-${this.authUser.id}`
            // );

            this.channel = await client.getChannelByUniqueName(
                `${this.otherUser.userDetailId}`
            );

            this.channel.on("messageAdded", message => {
                this.messages.push(message);
            });
        },
        sendMessage() {
            this.channel.sendMessage(this.newMessage);
            this.newMessage = "";
        },
        sendMediaMessage({ target }) {
            const formData = new FormData();
            formData.append('file', target.files[0]);
            this.channel.sendMessage(formData);
            target.value = "";
        },
        async fetchMessages() {
            const messages = (await this.channel.getMessages()).items;
            for (const message of messages) {
                if (message.type === 'media') {
                    const mediaUrl = await message.media.getContentUrl()
                    this.messages.push({
                        type: message.type,
                        author: message.author,
                        filename: message.media.filename,
                        mediaUrl,
                        created_at: message.dateUpdated,
                    })
                } else{
                    this.messages.push({
                        type: message.type,
                        author: message.author,
                        body: message.body,
                        created_at: message.dateUpdated,
                    })
                }
                // this.pushToArray(message)
            }
        },
        getAuthUserImage(){
            if(this.authUser.profile_image) {
                return this.authUser.profile_image;
            }else{
                return this.otherUser.siteURL+"/resources/uploads/profile/default.jpg";
            }
        },
        getReceptorImage(){
            if(this.otherUser.profile_image) {
                return this.otherUser.profile_image;
            }else{
                return this.otherUser.siteURL+"/resources/uploads/profile/default.jpg";
            }
        },
        humanize(date) {
            return moment(date).format('DD-MM-YY h:mma');
        },
        // async pushToArray (message) {
        //     if (message.type === 'media') {
        //         const mediaUrl = await message.media.getContentUrl()
        //         this.messages.push({
        //             type: message.type,
        //             author: message.author,
        //             filename: message.media.filename,
        //             mediaUrl
        //         })
        //     } else {
        //         this.messages.push({
        //             type: message.type,
        //             author: message.author,
        //             body: message.body,
        //         })
        //     }
           
        // },
    }
};
</script>
<style scoped>
    .direct-chat-img{
        object-fit: cover;
        border-radius: 50%;
        width: 50px;
        height: 50px;
    }
    .content{
        min-height: 0;
        padding-top: 0px;
    }
    .metadata-author{
        border-radius: 5px;
        position: relative;
        padding: 5px 10px;
        background: #d2d6de;
        border: 1px solid #d2d6de;
        margin: 5px 0 0 0;
        background: #3c8dbc;
        border-color: #3c8dbc;
        color: #fff;
    }
    .metadata-user{
        border-radius: 5px;
        position: relative;
        padding: 5px 10px;
        background: #d2d6de;
        border: 1px solid #d2d6de;
        margin: 5px 0 0 0;
        color: #444;
    }
    .metadata-author .author{
        color:#ffffff;
    }
    .metadata-user .author{
        color: #444;
    }
    .date{
        font-size: 12px;    
    }
</style>
