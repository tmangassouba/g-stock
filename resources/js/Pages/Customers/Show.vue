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
                <div class="column is-3">
                    <div>{{ _customer.type }}</div>
                    <hr>
                    <strong>{{ _customer.title }} {{ _customer.name }}</strong>

                    <div>
                        <b-icon icon="email-outline" size="is-small"></b-icon>
                        <a :href="'mailto:' + _customer.email" v-if="_customer.email">{{ _customer.email }}</a>
                        <span v-else>-</span>
                    </div>

                    <div>
                        <b-icon icon="phone-outline" size="is-small"></b-icon>
                        <a :href="'tel:' + _customer.telephone" v-if="_customer.telephone">{{ _customer.telephone }}</a>
                        <span v-else>-</span>
                    </div>

                    <div style="margin-top: 1rem;">
                        <strong>Adresse</strong><br>
                        <span v-if="_customer.address || _customer.city">
                            {{ _customer.address }}{{ _customer.address && _customer.city ? ', ' : '' }} {{ _customer.city }}
                        </span>
                        <span v-else>-</span>
                    </div>

                    <div style="margin-top: 1rem;">
                        <strong>Compagnie</strong> <br>
                        {{ _customer.company ? _customer.company : '-' }}
                    </div>

                    <div style="margin-top: 1rem;">
                        <strong>Site web</strong> <br>
                        <a :href="_customer.website" target="_blank" v-if="_customer.website">{{ _customer.website }}</a>
                        <span v-else>-</span>
                    </div>
                </div>

                <div class="column" style="border-left: 1px solid #eee;">
                    <div>
                        Facture impayées
                    </div>

                    <hr>

                    <b-tabs :animated="false">
                        <b-tab-item label="Factures">
                            Lorem ipsum dolor sit amet.
                        </b-tab-item>

                        <b-tab-item label="Documents" :disabled="true">
                        </b-tab-item>
                    </b-tabs>
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
                checkedRows: []
            }
        },

        methods: {
            deleteCustomer() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer clients',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce client ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer client',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/clients/delete-clients', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Client supprimé avec succès.',
                                        type: 'is-success'
                                    })
                                }
                            })
                            .catch(({message}) => {
                                // this.$handleMessage(message, 'danger');
                            }).finally(() => {
                                this.isDeleting = false
                            })
                        }
                    })
                }
            }
        },

        computed: {
            titleStack () {
                let title = this._customer ? this._customer.name : '-'
                return ['Client > ' + title]
            },
            _customer() {
                return this.customer && this.customer.data ? this.customer.data : null
            },
        },

        created() {
            if (this._customer) {
                this.checkedRows[0] = this._customer
            }
        },
    }
</script>