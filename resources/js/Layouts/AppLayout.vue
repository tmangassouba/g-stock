<template>
    <div>
        <nav-bar />
        <!-- <aside-menu :menu="menu" /> -->
        <aside-menu />
        <slot></slot>
        <!-- <footer-bar /> -->

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>
    </div>
    
</template>

<script>
    import { mapActions } from 'vuex'
    import NavBar from '../Menu/NavBar'
    import AsideMenu from '../Menu/AsideMenu'
    import FooterBar from '../Menu/FooterBar'

    export default {
        components: {
            NavBar,
            AsideMenu,
            FooterBar,
        },

        data() {
            return {
                //menu: []
            }
        },

        methods: {
            ...mapActions({
                getUser: 'user/getUser'
            })
            // switchToTeam(team) {
            //     this.$inertia.put('/current-team', {
            //         'team_id': team.id
            //     }, {
            //         preserveState: false
            //     })
            // },

            // logout() {
            //     axios.post('/logout').then(response => {
            //         window.location = '/';
            //     })
            // },
        },

        computed: {
            path() {
                return window.location.pathname
            }
        },

        created() {
            this.getUser()
        },

        // mounted() {
        //     this.getUser()
        // },
    }
</script>
