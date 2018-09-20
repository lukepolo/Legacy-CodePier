<template>
  <div class="parent">
    <section id="middle" class="column">
      <div class="column--content">
        <div class="container">
          <router-view name="nav" />
          <router-view />
        </div>
      </div>
    </section>
  </div>
</template>;
