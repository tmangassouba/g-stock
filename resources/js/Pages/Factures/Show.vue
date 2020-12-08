<template>
    <app-layout>
        <title-bar :title-stack="titleStack">
            <div class="buttons is-right" v-if="canEdit">
                <b-button class="is-info is-small" icon-left="pencil" tag="a" :href="'/factures/'+ _invoice.reference +'/modifier'">Modifier</b-button>
                <b-button class="is-danger is-small" icon-left="delete-outline" @click="deleteInvoice">Supprimer</b-button>
            </div>
        </title-bar>

        <section class="section is-main-section">
            {{ _invoice }}
        </section>
    </app-layout>
</template>

<script>
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
            }
        },

        computed: {
            titleStack () {
                let title = this._invoice ? this._invoice.reference : '-'
                return ['Factures > ' + title]
            },
            _invoice() {
                return this.invoice && this.invoice.data ? this.invoice.data : null
            },
        },

        created() {
            if (this._invoice) {
                this.checkedRows[0] = this._invoice
            }
        },
    }
</script>