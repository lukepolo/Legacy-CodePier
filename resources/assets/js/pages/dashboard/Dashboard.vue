<template>
    <section>
        <section id="left" class="section-column">
            <h3 class="section-header">Servers</h3>

            <div class="section-content">
                <div class="server text-center">
                    <button class="btn btn-primary">Create Server</button>
                </div>
            </div>
        </section>

        <section id="middle" class="section-column">
            Topology View?
       </section>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                 pile : localStorage.getItem('current_pile_id')
            }
        }
    }
</script>