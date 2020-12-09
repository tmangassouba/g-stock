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
                    <div class="colums">
                        <div class="column is-6" style="border-right: 1px solid #ddd !important;">
                            <h6 class="title is-6">Facture impayées</h6>
                            <div class="title is-5 has-text-danger">
                                {{ _customer.factureImpayee | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}
                            </div>
                        </div>
                        <div class="column"></div>
                    </div>

                    <!-- <hr> -->

                    <b-tabs :animated="false">
                        <b-tab-item label="Factures">
                            <b-table
                                :data="factures"
                                :loading="loadingInvoices"
                                striped
                                hoverable
                            >
                                <b-table-column field="date" label="Date" v-slot="props">
                                        {{ props.row.formated_date }}
                                </b-table-column>

                                <b-table-column field="reference" label="Nº Facture" v-slot="props">
                                    <inertia-link :href="'/factures/' + props.row.reference">
                                        {{ props.row.reference }}
                                    </inertia-link>
                                </b-table-column>

                                <b-table-column field="statut" label="Statut" v-slot="props">
                                    <b-tag :type="tagType(props.row.statut) + ' is-light'">{{ props.row.statut }}</b-tag>
                                </b-table-column>

                                <b-table-column field="date" label="Montant" numeric v-slot="props">
                                    {{ props.row.total | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}
                                </b-table-column>

                                <b-table-column field="acompte" label="Acompte" numeric v-slot="props">
                                    {{ props.row.acompte | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}
                                </b-table-column>

                                <template slot="empty">
                                    <section class="section">
                                        <div class="content has-text-grey has-text-centered">
                                            <div><b-icon icon="inbox" size="is-large"></b-icon></div>
                                            <p>Aucun résultat.</p>
                                        </div>
                                    </section>
                                </template>
                            </b-table>
                        </b-tab-item>

                        <!-- <b-tab-item label="Documents" :disabled="true">
                        </b-tab-item> -->
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
    import { mapActions, mapGetters } from 'vuex'

    export default {
        props: ['customer', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                checkedRows: [],
                factures: [],
                loadingInvoices: true
            }
        },

        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
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
            },
            getFactures() {
                this.loadingInvoices = true
                axios.get('/clients/' + this._customer.code + '/factures', null)
                .then(({data}) => {
                    this.factures = data.data
                    // console.log(data)
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.loadingInvoices = false
                })
            },
            tagType(status) {
                if (status == this.$INVOICE_PAYEE) {
                    return 'is-success'
                }
                if (status == this.$INVOICE_ACOMPTE) {
                    return 'is-warning'
                }
                if (status == this.$INVOICE_NON_PAYEE) {
                    return 'is-danger'
                }
                return null
            }
        },

        computed: {
            ...mapGetters({
                organisation: 'parametres/getParametre',
            }),
            titleStack () {
                let title = this._customer ? this._customer.name : '-'
                return ['Client > ' + title]
            },
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
            _customer() {
                return this.customer && this.customer.data ? this.customer.data : null
            }
        },

        created() {
            if (this._customer) {
                this.checkedRows[0] = this._customer
            }
            this.getFactures()
            this.getEntreprise()
        },
    }
</script>