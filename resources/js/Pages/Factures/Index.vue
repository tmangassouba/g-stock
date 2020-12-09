<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="can_edit">
                <b-button class="is-info is-small" icon-left="plus" tag="a" href="/factures/ajouter">Nouveau</b-button>
                <!-- <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteData" v-if="checkedRows.length">Supprimer</b-button> -->
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="invoices.data"
                :loading="loading"
                striped
                hoverable

                paginated
                pagination-simple
                backend-pagination
                :total="total"
                :per-page="perPage"
                :current-page="currentPage"
                @page-change="onPageChange"
                pagination-size="is-small"
                aria-next-label="Suivant"
                aria-previous-label="Précédent"
                aria-page-label="Page"
                aria-current-label="Page actuelle"

                backend-sorting
                :default-sort="[_sortField, _sortOrder]"
                @sort="onSort"
            >
                <b-table-column field="date" label="Date" sortable v-slot="props">
                        {{ props.row.formated_date }}
                </b-table-column>

                <b-table-column field="reference" label="Nº Facture" sortable v-slot="props">
                    <inertia-link :href="'/factures/' + props.row.reference">
                        {{ props.row.reference }}
                    </inertia-link>
                </b-table-column>

                <b-table-column field="statut" label="Statut" sortable v-slot="props">
                    <b-tag :type="tagType(props.row.statut) + ' is-light'">{{ props.row.statut }}</b-tag>
                </b-table-column>

                <b-table-column field="customer_id" label="Client" sortable v-slot="props">
                    <inertia-link :href="'/clients/' + props.row.customer.code" v-if="props.row.customer">
                        {{ props.row.customer.name }}
                    </inertia-link>
                    <span v-else>-</span>
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
        </section>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { Inertia } from '@inertiajs/inertia'
    import { mapActions, mapGetters } from 'vuex'

    export default {
        props: ['invoices', 'sortField', 'sortOrder', 'message', 'errors', 'can_edit'],
        components: {
            AppLayout,
            TitleBar,
        },
        data() {
            return {
                loading: false,
                // checkedRows: [],
                total: null,
                perPage: null,
                currentPage: null,
                _sortField: null,
                _sortOrder: null,
            }
        },

        methods: {
            ...mapActions({
                getEntreprise: 'parametres/getEntreprise'
            }),
            loadAsyncData() {
                Inertia.visit('/factures', {
                    method: 'get',
                    data: {
                        page: this.currentPage,
                        sortField: this._sortField,
                        sortOrder: this._sortOrder,
                    }
                })
            },
            onPageChange(page) {
                this.currentPage = page
                this.loadAsyncData()
            },
            onSort(field, order) {
                this._sortField = field
                this._sortOrder = order
                this.loadAsyncData()
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
                return ['Factures']
            },
            monaie() {
                return this.organisation ? this.organisation.devise : '-'
            },
        },

        created() {
            if (this.invoices) {
                this.currentPage = this.invoices.meta.current_page
                this._sortField = this.sortField
                this._sortOrder = this.sortOrder
                this.perPage = this.invoices.meta.per_page
                this.total = this.invoices.meta.total
            }
            this.getEntreprise()
        },
    }
</script>