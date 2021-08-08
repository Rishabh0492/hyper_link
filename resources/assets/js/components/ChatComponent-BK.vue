<template>
    <div class="card">
        <div class="card-header">{{ otherUser.first_name }}</div>

        <div class="card-body">
            <div v-for="message in messages" v-bind:key="message.id">
                <div v-if="message.type === 'media'" :class="{ 'text-right': message.author === authUser.email }">
                    <img class="img-thumbnail" :src="message.mediaUrl" :alt="message.filename" width="150px">
                </div>
                <div v-else>
                    <div :class="{ 'text-right': message.author === authUser.email }">
                        {{ message.body }}
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

        <!--<div class="card-footer">
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
        </div>-->

        <form @submit.prevent="sendMessage($event)" class="ui reply form" enctype="multipart/form-data">
            <div class="field">
                <textarea class="form-control" id="twemoji-textarea" placeholder="Write a message."  v-model="newMessage" />
            </div>
            <div class="field emoji-page imgUpload">
                <input type="file" class="form-control" id="poster" placeholder="Upload Image">
            </div>
            <div class="submit_btn" >
                <button type="submit" class="ui blue labeled submit icon button" style="float:right;">
                    <i class="send outline icon"></i> Send
                </button>
            </div>
        </form>
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
            channel: ""
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
        sendMessage(e) {
            var newMessage = this.newMessage;
            var file = $('#poster').get(0).files[0];
            if(newMessage.length > 0) {
                this.channel.sendMessage(newMessage);
            } else {
                alert('Please Type message');
            }
        },
        sendMediaMessage({ target }) {
            alert(target);
            const formData = new FormData();
            formData.append('file', target.files[0]);
            this.channel.sendMessage(formData);
            target.value = "";
        },
        async fetchMessages() {
            const messages = (await this.channel.getMessages()).items;
            for (const message of messages) {
                this.pushToArray(message)
            }
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