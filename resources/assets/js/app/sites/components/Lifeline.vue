<template>
    <div class="lifeline">

        <div class="flyform--group">
            <div class="flex">
                <div class="flex--grow flex--shrink">
                    <label>{{ lifeLine.name }}</label>
                    <textarea class="flex--grow" rows="3" readonly>https://lifelines.codepier.io{{ lifeLine.url }}</textarea>

                    <div class="flex">
                        <div class="flex--grow flex--shrink">
                            <template v-if="lifeLine.last_seen">
                                <div class="status status--success"></div>
                                <time-ago :time="lifeLine.last_seen"></time-ago>
                            </template>

                            <template v-else>
                                <div class="status status--neutral"></div>
                                Never Seen
                            </template>
                        </div>

                        <small>Check every {{ lifeLine.threshold }} minutes</small>
                    </div>
                </div>

                <div>
                    <tooltip message="Copy to Clipboard" class="flyform--btn-right">
                        <clipboard :data="lifeLine.url"></clipboard>
                    </tooltip>

                    <br>

                    <tooltip message="Delete" class="flyform--btn-right">
                        <confirm dispatch="user_site_life_lines/destroy" confirm_class="btn btn-small" :params="{ site : site.id, life_line : lifeLine.id }">
                            <span class="icon-trash"></span>
                        </confirm>
                    </tooltip>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props : ['lifeLine', 'site'],
    }
</script>