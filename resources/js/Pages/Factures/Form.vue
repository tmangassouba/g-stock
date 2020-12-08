<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right">
                <span v-if="editMode">
                    <b-button class="is-small" icon-left="arrow-left" tag="a" :href="'/factures/' + _invoice.reference">Annuler</b-button>
                    <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteInvoice()">Supprimer</b-button>
                </span>
                <b-button class="is-small" icon-left="arrow-left" tag="a" href="/factures" v-else>Annuler</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <form @submit.prevent="editMode ? updateData() : addData()" v-if="form">
                <!-- {{ form }} -->
                <div class="columns">
                    <div class="column is-6">
                        <b-field
                            horizontal
                            label="Client"
                            :type="$page.errors.date ? 'is-danger' : ''"
                            :message="$page.errors.date ? $page.errors.date[0] : ''"
                            class="field-label is-small">
                            <b-autocomplete
                                :data="customers"
                                field="name"
                                :loading="loadingCustomers"
                                name="customer_name"
                                size="is-small"
                                v-model="form.customer_name"
                                @typing="getAsyncCustomers"
                                required
                                autocomplete="off"
                                @select="option => { form.customer_id = option ? option.id : null; customers = []}">
                                <template slot-scope="props">
                                    {{ props.option.name }}
                                    <br>
                                    <span class="has-text-grey-light">{{ props.option.company }}</span>
                                </template>
                                <template slot="header" v-if="gerant">
                                    <a a="/clients/ajouter" target="_blank">
                                        <span> Nouveau client </span>
                                    </a> 
                                </template>
                                <template slot="empty">Aucun résultat.</template>
                            </b-autocomplete>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Date"
                            :type="$page.errors.date ? 'is-danger' : ''"
                            :message="$page.errors.date ? $page.errors.date[0] : ''"
                            class="field-label is-small">
                            <b-datepicker
                                v-model="form.date"
                                locale="fr-FR"
                                placeholder="Date"
                                icon="calendar-today"
                                size="is-small"
                                trap-focus
                                required>
                            </b-datepicker>
                        </b-field>
                        
                        <b-field
                            horizontal 
                            label="Statut"
                            :type="$page.errors.statut ? 'is-danger' : ''"
                            :message="$page.errors.statut ? $page.errors.statut[0] : ''"
                            class="field-label is-small">
                            <div>
                                <b-radio name="statut" v-model="form.statut" :native-value="$INVOICE_PAYEE" type="is-info" required> {{ $INVOICE_PAYEE }} </b-radio>
                                <b-radio name="statut" v-model="form.statut" :native-value="$INVOICE_ACOMPTE" type="is-info" required> {{ $INVOICE_ACOMPTE }} </b-radio>
                                <b-radio name="statut" v-model="form.statut" :native-value="$INVOICE_NON_PAYEE" type="is-info" required> {{ $INVOICE_NON_PAYEE }} </b-radio>
                            </div>
                        </b-field>

                        <b-field
                            horizontal 
                            label="Description"
                            :type="$page.errors.description ? 'is-danger' : ''"
                            :message="$page.errors.description ? $page.errors.description[0] : ''"
                            class="field-label is-small">
                            <b-input name="description" v-model="form.description" expanded type="textarea"></b-input>
                        </b-field>

                    </div>

                    <div class="column"></div>
                </div>

                <hr>

                <b-table
                    :data="form.products"
                    :striped="true"
                    :hoverable="false"
                    :narrowed="false"
                    :mobile-cards="true">

                    <!-- <template slot-scope="props"> -->
                        <b-table-column field="produit_name" label="Produit" v-slot="props">
                            <b-autocomplete
                                :data="produits"
                                field="designation"
                                :loading="loadingProducts"
                                name="produit_name"
                                size="is-small"
                                v-model="props.row.produit_name"
                                @typing="getAsyncData"
                                required
                                @select="option => {props.row.produit_id = option ? option.id : null; props.row.prix = option ? option.prix : 1}">
                                <template slot-scope="props">{{ props.option.designation }}</template>
                                <template slot="header" v-if="gerant">
                                    <a @click="isModalProductActive = true">
                                        <span> Nouvel produit </span>
                                    </a> 
                                </template>
                                <template slot="empty">Aucun résultat.</template>
                            </b-autocomplete>
                        </b-table-column>

                        <b-table-column field="quantite" label="Quantité" :width="150" numeric v-slot="props">
                            <b-input name="quantite" v-model="props.row.quantite" type="number" size="is-small" style="direction: rtl;" required></b-input>
                        </b-table-column>

                        <b-table-column field="" label="P.U" :width="150" numeric v-slot="props">
                            <b-input name="prix" v-model="props.row.prix" type="number" size="is-small" style="direction: rtl;" required></b-input>
                        </b-table-column>

                        <b-table-column field="" label="Sous total" :width="150" numeric v-slot="props">
                            {{ (props.row.prix * props.row.quantite) | number('0,0', { thousandsSeparator: ' ' }) }}
                        </b-table-column>

                        <b-table-column field="" label="" :width="50" align="right" numeric v-slot="props">
                            <b-button icon-left="delete" size="is-small is-light" type="is-danger" @click="removeData(props.index)" :disabled="form.products.length <= 1"></b-button>
                        </b-table-column>
                    <!-- </template> -->
                </b-table>

                <b-button type="is-text" @click="addLine">Ajouter une ligne</b-button>

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

                <hr>
                <b-field>
                    <b-button size="is-small" type="is-info" native-type="submit" :loading="isSaving">{{ title }}</b-button>
                </b-field>
            </form>

            <b-modal 
                v-model="isModalProductActive"
                trap-focus
                :destroy-on-hide="false"
                :can-cancel="['escape', 'x']"
                :width="640"
            >
                <article-form :article="{}" @close="isModalProductActive = false"></article-form>
            </b-modal>
        </section>
    </app-layout>
</template>

<script>
    import { Inertia } from '@inertiajs/inertia'
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { ArticleForm } from "../../components/Articles"
    import { mapActions, mapGetters } from 'vuex'
    import debounce from 'lodash/debounce'

    export default {
        props: ['invoice', 'message', 'errors', 'gerant'],
        components: {
            AppLayout,
            TitleBar,
            ArticleForm
        },
        data() {
            return {
                isSaving: false,
                isModalProductActive: false,
                loadingProducts: false,
                loadingCustomers: false,
                checkedRows: [],
                produits: [],
                customers: [],
                form: {
                    id: null,
                    reference: null,
                    statut: null,
                    customer_id: null,
                    customer_name: null,
                    date: new Date(),
                    description: null,
                    products: [
                        {
                            produit_id: null,
                            produit_name: null,
                            quantite: 1,
                            prix: 1,
                        }
                    ]
                }
            }
        },

        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
            addData() {
                this.isSaving = true
                this.$inertia.post('/factures', this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Ajoutée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.isSaving = false
                })
            },
            updateData() {
                this.isSaving = true
                this.$inertia.put('/factures/' + this._invoice.reference, this.form)
                .then(() => {
                    if (this.$page.flash.message != null ) {
                        this.$buefy.notification.open({
                            message: 'Modifiée avec succès.',
                            type: 'is-success'
                        })
                    }
                })
                .catch(({message}) => {
                    // this.$handleMessage(message, 'danger');
                }).finally(() => {
                    this.isSaving = false
                })
            },
            deleteInvoice() {
                this.$buefy.dialog.confirm({
                    title: 'Supprimer facture',
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
                                    message: 'Facture supprimée avec succès.',
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
            },
            addLine() {
                this.form.products.push(
                    {
                        produit_id: null,
                        produit_name: null,
                        quantite: 1,
                        prix: 1
                    }
                )
            },
            removeData(index) {
                if (this.form.products && this.form.products.length > 1) {
                    this.form.products.splice(index, 1)
                }
            },
            getAsyncData: debounce(function (name) {
                if (!name.length) {
                    return
                }
                this.loadingProducts = true
                axios.get('/api/produits', { params: { recherche: name } })
                .then(({data}) => {
                    this.produits = data.data
                })
                .catch(response => {
                    this.produits = []
                    this.$handleMessage(response.message, 'danger')
                })
                .finally(() => {
                    this.loadingProducts = false
                });
            }, 500),
            getAsyncCustomers: debounce(function (name) {
                if (!name.length) {
                    return
                }
                this.loadingCustomers = true
                axios.get('/api/clients', { params: { recherche: name } })
                .then(({data}) => {
                    this.customers = data.data
                })
                .catch(response => {
                    this.customers = []
                    this.$handleMessage(response.message, 'danger')
                })
                .finally(() => {
                    this.loadingCustomers = false
                });
            }, 500)
        },

        computed: {
            ...mapGetters({
                organisation: 'parametres/getParametre',
            }),
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
            totalFacture: function () {
                let total = 0
                if (this.form && this.form.products) {
                    this.form.products.forEach(produit => {
                        total += produit.quantite * produit.prix
                    });
                }
                return total
            },
            title() {
                return this.editMode ? 'Modifier' : 'Ajouter'
            },
            titleStack () {
                return [this.title + ' facture']
            },
            _invoice() {
                return this.invoice && this.invoice.data ? this.invoice.data : null
            },
            editMode() {
                return this._invoice && this._invoice.id ? true : false;
            },
        },

        created() {
            this.getEntreprise()
            if (this._invoice) {
                this.form.id            = this._invoice.id,
                this.form.reference     = this._invoice.reference,
                this.form.statut        = this._invoice.statut,
                this.form.customer_id   = this._invoice.customer_id,
                this.form.customer_name = this._invoice.customer ? this._invoice.customer.name : null,
                this.form.date          = new Date(this._invoice.date),
                this.form.description   = this._invoice.description
                let index = 0

                this._invoice.products.forEach(element => {
                    let prod = {
                        produit_id: element.pivot ? element.id : null,
                        produit_name: element.pivot ? element.designation : null,
                        quantite: element.pivot ? element.pivot.quantite : null,
                        prix: element.pivot ? element.pivot.prix : null,
                    }
                    this.form.products[index] = prod
                    index ++
                });
            }
        },
    }
</script>