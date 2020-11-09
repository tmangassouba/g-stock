<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="admin">
                <b-button class="is-info is-small" icon-left="plus" @click="isModalActive = true">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteProducts" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <!-- <b-notification
                v-if="message"
                type="is-success"
                has-icon
                aria-close-label="Close notification">
                {{ message }}
            </b-notification> -->

            <b-table 
                :data="products.data"
                :loading="loading"
                striped
                hoverable

                checkable
                :checked-rows.sync="checkedRows"

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
                <!-- :default-sort-direction="defaultSortOrder" -->
                <b-table-column field="code" label="Ref." width='150' sortable v-slot="props">
                    <inertia-link :href="'/articles/' + props.row.code">{{ props.row.code }}</inertia-link>
                </b-table-column>
                <b-table-column field="designation" label="Désignation" sortable v-slot="props">
                    <inertia-link :href="'/articles/' + props.row.code">{{ props.row.designation }}</inertia-link>
                </b-table-column>
                <b-table-column label="Unité" v-slot="props">
                    <span v-if="props.row.unite">{{ props.row.unite.name }}</span>
                </b-table-column>
                <b-table-column label="Quantité" numeric>
                    <template v-slot:header="{ column }">
                        <b-tooltip label="Nb de pièces par unité." type="is-dark" append-to-body dashed>
                            {{column.label}}
                        </b-tooltip>
                    </template>
                    <template v-slot="props">{{ props.row.quantite }}</template>
                </b-table-column>
                <b-table-column field="prix" label="Prix de vente" sortable numeric v-slot="props">
                    <span>{{ props.row.prix }}</span>
                </b-table-column>
                <b-table-column field="stock" label="Stock disponible" numeric v-slot="props">
                    <span class="has-text-weight-bold" :class="props.row.stock < props.row.stock_min ? 'has-text-danger' : 'has-text-success-' ">
                        {{ props.row.stock }}
                    </span>
                </b-table-column>

                <template slot="empty">
                    <section class="section">
                        <div class="content has-text-grey has-text-centered">
                            <p><b-icon icon="inbox" size="is-large"></b-icon></p>
                            <p>Aucun résultat.</p>
                        </div>
                    </section>
                </template>
            </b-table>
        </section>

        <b-modal 
            v-model="isModalActive"
            trap-focus
            :destroy-on-hide="false"
            :can-cancel="['escape', 'x']"
            :width="640"
        >
            <article-form :article="selectArticle" @resetData="selectArticle = {}" @close="isModalActive = false"></article-form>
        </b-modal>

        <b-notification :closable="false" class="loading-notification">
            <b-loading :is-full-page="isFullPage" v-model="isDeleting" :can-cancel="false"></b-loading>
        </b-notification>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { ArticleForm } from "../../components/Articles"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['products', 'sortField', 'sortOrder', 'message', 'errors', 'admin'],
        components: {
            AppLayout,
            TitleBar,
            ArticleForm
        },
        data() {
            return {
                selectArticle: {},
                isModalActive: false,
                loading: false,
                checkedRows: [],
                total: null,
                perPage: null,
                currentPage: null,
                _sortField: null,
                _sortOrder: null,
                defaultSortOrder: 'asc',

                isFullPage: true,
                isDeleting: false,
            }
        },
        
        methods: {
            loadAsyncData() {
                Inertia.visit('/articles', {
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
            deleteProducts() {
                if (this.checkedRows.length) {
                    this.$buefy.dialog.confirm({
                        title: 'Supprimer articles',
                        message: 'Etes-vous sûrs de vouloir <b>supprimer</b> ce(t)(s) article(s) ?<br/> Cette action ne peut pas être annulée.',
                        confirmText: 'Supprimer article(s)',
                        type: 'is-danger',
                        hasIcon: true,
                        size: 'is-small',
                        onConfirm: () => {
                            // this.$buefy.toast.open('Account deleted!')
                            this.isDeleting = true
                            var checkedForm = {
                                checkedRows: this.checkedRows
                            }
                            this.$inertia.post('/articles/delete-products', checkedForm)
                            .then(() => {
                                if (this.$page.flash.message != null ) {
                                    // this.resetForm()
                                    this.$buefy.notification.open({
                                        message: 'Article(s) supprimé(s) avec succès.',
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
        created() {
            if (this.products) {
                this.currentPage = this.products.current_page
                this._sortField = this.sortField
                this._sortOrder = this.sortOrder
                this.perPage = this.products.per_page
                this.total = this.products.total
            }
        },

        computed: {
            // _sortField: function () {
            //     return this.sortField
            // },
            // _sortOrder: function () {
            //     return this.sortOrder
            // },
            titleStack () {
                return ['Articles']
            }
        },

        // watch: {
        //     checkedData: function () {
        //         this.deleteUsersForm.checkedData = this.checkedData
        //     }
        // },
    }
</script>
