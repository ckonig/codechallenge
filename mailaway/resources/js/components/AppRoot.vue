<template>
  <div>
    <h1>Mailaway</h1>
    <div class="container">
      <div class="row">
        <div class="col-lg-9">
          <send-mail-form></send-mail-form>
        </div>
        <div class="col-lg">
          <check-mail-form></check-mail-form>
        </div>
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
