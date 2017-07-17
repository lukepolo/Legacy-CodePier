<template>
    <section>
        {{ lifeLine.name }} - {{ lifeLine.url }}<br>
        <template v-if="lifeLine.last_seen">
            <time-ago :time="lifeLine.last_seen"></time-ago>
        </template>
        <template v-else>
            Never Seen
        </template>
        <br>
        <confirm dispatch="user_site_life_lines/destroy" confirm_class="btn btn-small" :params="{ site : site.id, life_line : lifeLine.id }">
            delete
        </confirm>
    </section>
</template>

<script>
    export default {
        props : ['lifeLine', 'site'],
    }
</script>