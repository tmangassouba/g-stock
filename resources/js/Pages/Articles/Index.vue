<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right">
                <b-button class="is-info is-small" icon-left="plus" @click="isModalActive = true">Nouveau</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="isModalActive = false" v-if="checkedRows.length">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            <b-table 
                :data="products.data"
                :loading="loading"
                striped
                narrowed
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
                    <inertia-link :href="'/article/' + props.row.code">{{ props.row.code }}</inertia-link>
                </b-table-column>
                <b-table-column field="designation" label="Désignation" sortable v-slot="props">
                    <inertia-link :href="'/article/' + props.row.code">{{ props.row.designation }}</inertia-link>
                </b-table-column>
                <b-table-column field="id" label="Stock disponible" numeric v-slot="props">
                    {{ props.row.id }}
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
            <article-form @close="isModalActive = false"></article-form>
        </b-modal>
    </app-layout>
</template>

<script>
    import AppLayout from '../../Layouts/AppLayout'
    import TitleBar from '../../Menu/TitleBar'
    import { ArticleForm } from "../../components/Articles"
    import { Inertia } from '@inertiajs/inertia'

    export default {
        props: ['products', 'sortField', 'sortOrder', 'errors'],
        components: {
            AppLayout,
            TitleBar,
            ArticleForm
        },
        data() {
            return {
                isModalActive: false,
                loading: false,
                checkedRows: [],
                total: null,
                perPage: null,
                currentPage: null,
                _sortField: null,
                _sortOrder: null,
                defaultSortOrder: 'asc',
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
