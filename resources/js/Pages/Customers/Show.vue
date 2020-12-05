<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="gerant">
                <b-button class="is-info is-small" icon-left="pencil" tag="a" :href="'/clients/'+ _customer.code +'/modifier'">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteCustomer">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            {{ _customer }}
        </section>
    </app-layout>
</template>

<script>
    import { Inertia } from '@inertiajs/inertia'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'

    export default {
        props: ['customer', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                //
            }
        },

        methods: {
            deleteCustomer() {
                //
            }
        },

        computed: {
            titleStack () {
                return [' client']
            },
            _customer() {
                return this.customer && this.customer.data ? this.customer.data : null
            },
        },

        created() {
            //
        },
    }
</script>