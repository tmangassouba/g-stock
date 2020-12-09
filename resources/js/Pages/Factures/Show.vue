<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="canEdit">
                <b-button class="is-info is-small" icon-left="pencil" tag="a" :href="'/factures/'+ _invoice.reference +'/modifier'">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteInvoice">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <div>
                <b-field
                    horizontal 
                    label="Num. Référence">
                    {{ _invoice.reference }}
                </b-field>
                
                <b-field
                    horizontal 
                    label="Client">
                    <inertia-link :href="'/clients/' + _invoice.customer.code" v-if="_invoice.customer">
                        {{ _invoice.customer.name }}
                    </inertia-link>
                    <span v-else></span>
                </b-field>
                
                <b-field
                    horizontal 
                    label="Date">
                    {{ _invoice.formated_date }}
                </b-field>
                
                <b-field
                    horizontal 
                    label="Statut">
                    <b-tag :type="tagType(_invoice.statut) + ' is-light'">{{ _invoice.statut }}</b-tag>
                </b-field>
                
                <b-field
                    horizontal 
                    label="Acompte">
                    {{ _invoice.acompte | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}
                </b-field>

                <b-field
                    horizontal 
                    label="Description">
                    {{ _invoice.description ? _invoice.description : '-' }}
                </b-field>
            </div>

            <hr>

            <b-table
                v-if="_invoice"
                :data="_invoice.products"
                :striped="true"
                :hoverable="false"
                :narrowed="false"
                :mobile-cards="true">

                    <b-table-column field="produit_name" label="Produit" v-slot="props">
                        <inertia-link :href="'/articles/' + props.row.code">
                            {{ props.row.designation ? props.row.designation : '-' }}
                        </inertia-link>
                    </b-table-column>

                    <b-table-column field="quantite" label="Quantité" :width="150" numeric v-slot="props">
                        {{ props.row.pivot ? props.row.pivot.quantite : '-' }}
                        <small v-if="props.row.unite"> <br> {{ props.row.unite.name }}</small>
                    </b-table-column>

                    <b-table-column field="" label="P.U" :width="150" numeric v-slot="props">
                        {{ props.row.pivot ? props.row.pivot.prix : '-' }}
                    </b-table-column>

                    <b-table-column field="" label="Sous total" :width="150" numeric v-slot="props">
                        <span v-if="props.row.pivot">
                            {{ (props.row.pivot.prix * props.row.pivot.quantite) | number('0,0', { thousandsSeparator: ' ' }) }}
                        </span>
                    </b-table-column>
            </b-table>

            <div class="columns">
                <div class="column is-8">
                    <!-- {{ totalFacture }} -->
                </div>

                <div class="column is-4"  style="padding-top: 30px;">
                    <div class="info-row has-text-weight-bold" style="text-align:right;padding-right: 20px;">
                        <div class="has-text-grey">Total</div>
                        <div class="">
                            {{  totalFacture | number('0,0', { thousandsSeparator: ' ' }) }} {{ monaie }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </app-layout>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import { Inertia } from '@inertiajs/inertia'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'

    export default {
        props: ['invoice', 'message', 'errors', 'canEdit'],
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
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
            deleteInvoice() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer clients',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> cette facture ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer facture',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.delete('/factures/' + this._invoice.reference)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Supprimés avec succès.',
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
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
            titleStack () {
                let title = this._invoice ? this._invoice.reference : '-'
                return ['Factures #' + title]
            },
            _invoice() {
                return this.invoice && this.invoice.data ? this.invoice.data : null
            },
            totalFacture: function () {
                let total = 0
                if (this._invoice && this._invoice.products) {
                    this._invoice.products.forEach(produit => {
                        if (produit.pivot) {
                            total += produit.pivot.quantite * produit.pivot.prix
                        }
                    });
                }
                
                return total
            },
        },

        created() {
            if (this._invoice) {
                this.checkedRows[0] = this._invoice
            }
            this.getEntreprise()
        },
    }
</script>