<template>
  <div>
    <div v-if="activeMail">
      <transition name="modal">
        <div class="modal-mask">
          <div class="modal-wrapper">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">#{{activeMail.id}}:{{activeMail.title}}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" @click="setActiveMail(false)">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <p>Created: {{activeMail.created_at}}</p>
                  <p>Updated: {{activeMail.updated_at}}</p>
                  <p>Status: {{activeMail.status}}</p>
                  <p>From: {{activeMail.fromName}} ({{activeMail.fromEmail}})</p>
                  <p>Recipients:</p>
                  <ul v-for="(to, index) in JSON.parse(activeMail.to)" v-bind:key="index">
                    <li>{{to}}</li>
                  </ul>
                  <p>Text Body</p>
                  <pre>{{activeMail.body_txt}}</pre>
                  <p>HTML Body</p>
                  <div v-html="activeMail.body_html"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import store from "../stores";
export default {
  computed: {
    activeMail() {
      return store.getters.getActiveMail;
    }
  },
  methods: {
    setActiveMail(id) {
      store.dispatch("setActiveMail", { id });
    }
  }
};
</script>
