<template>
    <div class="card">
        <header v-if="title" class="card-header">
            <p class="card-header-title"> {{ title }} </p>
            <button type="button" class="delete" @click="$emit('close')"/>
        </header>
        <div class="card-content">
            <slot />
        </div>
    </div>
</template>

<script>
    export default {
        name: 'CardComponent',
        props: {
            title: {
                type: String,
                default: null
            },
        },
        methods: {
            //
        }
    }
</script>
