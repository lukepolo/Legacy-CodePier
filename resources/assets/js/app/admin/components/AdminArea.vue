<template>
    <div class="parent">
        <section id="middle" class="section-column">
            <div class="section-content">
                <div class="container">
                    <router-view name="nav"></router-view>
                    <router-view></router-view>
                </div>
            </div>
        </section>
    </div>
</template>