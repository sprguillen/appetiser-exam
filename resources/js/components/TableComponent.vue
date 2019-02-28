<template>
  <div class="container">
    <b-jumbotron bg-variant="light">
      <slot name="header">
        <h2>Appetiser Coding Challenge</h2>
      </slot>
      <slot name="lead">
        <h4>2018 List of PSHS NCE Passers</h4>
      </slot>
      <b-row class="mb-2">
        <b-col md="6">
          <b-form-select v-model="searchForm.selectedCriteria" :options="options" />
        </b-col>
        <b-col md="6">
          <b-form-input @input="search($event)" type="text" placeholder="Enter search term" />
        </b-col>
      </b-row>
      <b-table :items="paginatedData" stacked="md" striped hover responsive></b-table>
      <b-pagination size="md" :total-rows="items.length" v-model="pageNumber" :per-page="size" />
      <b-button variant="outline-primary" @click="exportData()">Export from PSHS NCE Passers</b-button>
      <b-button variant="outline-success" v-b-modal.addModal>Add New Examinee</b-button>

      <!-- Modal -->
      <b-modal id="addModal" ref="addModal" @ok="add">
        <fieldset>
          <b-form-input class="mb-1" v-model="addForm.name" type="text" placeholder="Name of Examinee" />
          <b-form-input class="mb-1" v-model="addForm.campusEligibility" type="text" placeholder="Campus Eligibility" />
          <b-form-input class="mb-1" v-model="addForm.school" type="text" placeholder="School" />
          <b-form-input class="mb-1" v-model="addForm.division" type="text" placeholder="Division" />
        </fieldset>
      </b-modal>
    </b-jumbotron>
  </div>
</template>
<script>
import { mapActions, mapGetters, mapMutations } from 'vuex';

export default {
  data() {
    return {
      items: [],
      fields: [
        {
          key: 'name_of_examinee',
          sortable: true,
          label: 'Name of Examinee'
        },
        {
          key: 'campus_eligibility',
          sortable: true,
          label: 'Campus Eligibility'
        },
        {
          key: 'school',
          sortable: true
        },
        {
          key: 'division',
          sortable: true
        }
      ],
      pageNumber: 1,
      size: 51,
      searchForm: {
        selectedCriteria: 'name_of_examinee'
      },
      addForm: {
        name: '',
        campusEligibility: '',
        school: '',
        division: ''
      },
      options: [
        { value: 'name_of_examinee', text: 'Name of Examinee' },
        { value: 'school', text: 'School' },
        { value: 'division', text: 'Division' }
      ]
    }
  },
  methods: {
    ...mapActions(['scrapeDataFromUrl', 'fetchFromDatabase', 'addPassers']),
    ...mapGetters(['getPassers']),
    ...mapMutations(['clearPassers']),
    async exportData() {
      const { data } = await this.scrapeDataFromUrl();
      await this.fetchFromDatabase();
      this.items = this.getPassers();
      this.$toasted.success(data.message);
    },
    search(evt) {
      let newItems = [],
      value = evt.toLowerCase();

      if (value.length > 0) {
        this.items.forEach((item) => {
          let sortedItem = item[this.searchForm.selectedCriteria].toLowerCase();
          if (sortedItem.includes(value)) {
            newItems.push(item);
          }
        });

        this.items = newItems;
      } else {
        this.items = this.getPassers();
      }
      
    },
    async resetPassers() {
      await this.fetchFromDatabase();
      this.items = this.getPassers();
    },
    async add(evt) {
      if (this.addForm.name
        && this.addForm.campusEligibility
        && this.addForm.school
        && this.addForm.division
      ) {
        evt.preventDefault();

        let data = {
          'name_of_examinee': this.addForm.name,
          'campus_eligibility': this.addForm.campusEligibility,
          'school': this.addForm.school,
          'division': this.addForm.division
        };
        await this.addPassers(data);
        this.clearPassers();
        this.clearAddForm();

        await this.resetPassers();

        this.$toasted.success('Added new examinee.');
        this.$refs.addModal.hide();
      } else {
        this.$toasted.info('Please fill up all fields.');
      }
    },
    clearAddForm() {
      this.addForm.name = null;
      this.addForm.campusEligibility = null;
      this.addForm.school = null;
      this.addForm.division = null;
    }
  },
  async beforeMount() {
    await this.resetPassers();
  },
  computed: {
    pageCount() {
      const length = this.items.length,
        size = this.size;
      return Math.ceil(length / size);
    },
    paginatedData() {
      const pageNumber = this.pageNumber - 1;
      const start = pageNumber * this.size,
        end = start + this.size;
      return this.items.slice(start, end);
    },
    disableSearch() {
      return !this.searchForm.searchText ? true : false;
    }
  }
}
</script>
<style scoped>
.container {
  margin: 0 auto;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
}
</style>
