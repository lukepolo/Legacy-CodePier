<template>
    <confirm dispatch="user_sites/destroy" :params="site.id" :confirm_with_text="site.name"
             confirm_class="btn-link btn-link-danger"
             confirm_position="bottom"
             confirm_message="Delete Site"
             confirm_text="Delete Site"
    >
        <tooltip message="Delete Site" placement="bottom">
            <span class="icon-trash"></span>
        </tooltip>
    </confirm>
</template>

<script>
    export default {
        props : ['site']
    }
</script>