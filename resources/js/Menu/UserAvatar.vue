<template>
    <div class="is-user-avatar">
        <img :src="avatar" alt="" />
    </div>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        name: 'UserAvatar',
        props: {
            avatar: {
                type: String,
                default: null
            }
        },
        data() {
            return {
                // userAvatar: '',
                // userName: ''
            }
        },
        computed: {
            // newUserAvatar () {
            //     return `https://avatars.dicebear.com/v2/human/${name}.svg?options[mood][]=happy`
            // },
            // ...mapState(['userAvatar', 'userName'])
        }
    }
</script>
