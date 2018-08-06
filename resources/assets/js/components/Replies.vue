<template>
    <div>
        <reply :reply="reply" 
            :key="reply.id" 
            v-for="(reply, index) in items"
            @deleted="remove(index)"></reply>
        
        <paginator :dataSet="dataSet" @updated="fetch"></paginator>

        <p v-if="$parent.locked">Thread is locked.</p>
        <new-reply @created="add" v-else></new-reply>
    </div>
</template>

<script>
import Reply from "./Reply";
import NewReply from './NewReply';
import collection from '../mixins/Collection';
import URLSearchParams from 'url-search-params';

export default {
  components: { Reply, NewReply },
  mixins: [collection],
  data() {
    return {
      dataSet: false,
    };
  },
  created() {
      this.fetch();
  },
  methods: {
      fetch(page) {
          if(!page) {
              let urlParams = new URLSearchParams(location.search);
              page = urlParams.has('page') ? urlParams.get('page') : 1;
          } 
          axios(this.url(page))
            .then(this.refresh);
      },
      url(page) {
          return `${location.pathname}/replies?page=${page}`;
      },
      refresh({ data }) {
          this.dataSet = data;
          this.items = data.data;

          window.scrollTo(0, 0);
      },
      
  }
};
</script>