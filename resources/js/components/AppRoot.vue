<template>
  <div>
    <div class="container">
      <header class="navbar navbar-dark bg-dark">
        <a class="navbar-brand" href="#">
          <h1>mailaway</h1>
        </a>
        <check-mail-form></check-mail-form>
      </header>

      <div class="row">&nbsp;</div>
      <div class="row">
        <main class="col-lg-9">
            <active-mail-display></active-mail-display>
          <send-mail-form></send-mail-form>
        </main>
        <aside class="col-lg">
          <recent-mail-list></recent-mail-list>
        </aside>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    console.log("Root component mounted.");
  }
};
</script>
