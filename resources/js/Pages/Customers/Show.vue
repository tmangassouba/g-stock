<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="gerant">
                <b-button class="is-info is-small" icon-left="pencil" tag="a" :href="'/clients/'+ _customer.code +'/modifier'">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteCustomer">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <div class="columns">
                <div class="column">
                    <b-field
                        horizontal 
                        label="Type">
                        {{ _customer.type }}
                    </b-field>

                    <b-field
                        horizontal 
                        label="Prénom">
                        {{ _customer.fisrt_name }}
                    </b-field>

                    <b-field
                        horizontal 
                        label="Adresse">
                        <span v-if="_customer.address">{{ _customer.address }}</span>
                        <span v-else></span>
                    </b-field>

                    <b-field
                        horizontal 
                        label="Fixe">
                        <a :href="'tel:' + _customer.telephone" v-if="_customer.telephone">{{ _customer.telephone }}</a>
                        <span v-else>-</span>
                    </b-field>

                    <b-field
                        horizontal 
                        label="Compagnie">
                        <span v-if="_customer.company">{{ _customer.company }}</span>
                        <span v-else></span>
                    </b-field>

                    <b-field
                        horizontal 
                        label="Email">
                        <a :href="'mailto:' + _customer.email" v-if="_customer.email">{{ _customer.email }}</a>
                        <span v-else>-</span>
                    </b-field>
                </div>
                <div class="column">
                    <b-field
                        horizontal 
                        label="Titre">
                        {{ _customer.title }}
                    </b-field>

                    <b-field
                        horizontal 
                        label="Nom">
                        {{ _customer.last_name }}
                    </b-field>

                    <b-field
                        horizontal 
                        label="Ville">
                        <span v-if="_customer.city">{{ _customer.city }}</span>
                        <span v-else></span>
                    </b-field>

                    <b-field
                        horizontal 
                        label="Mobile">
                        <a :href="'tel:' + _customer.mobile" v-if="_customer.mobile">{{ _customer.mobile }}</a>
                        <span v-else>-</span>
                    </b-field>

                    <b-field
                        horizontal 
                        label="Site web">
                        <a :href="_customer.website" target="_blank" v-if="_customer.website">{{ _customer.website }}</a>
                        <span v-else>-</span>
                    </b-field>
                </div>
            </div>
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