<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Dashboard - Update Profile
            </div>
          <div class="card-body">
            <form @submit.prevent="updateUser">
              <div class="form-group">
                <label for="name">Name</label>
                <input v-model="form.name" type="text" class="form-control" id="name" placeholder="Name" />
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input v-model="form.email" type="email" class="form-control" id="email" placeholder="Email" />
              </div>
              <div class="form-group">
                <label for="name">Address</label>
                <vue-google-autocomplete id="map" classname="form-control" placeholder="Address" v-on:placechanged="getAddressData"></vue-google-autocomplete>
                <span class="text-sm">{{ form.address }}</span>
              </div>
              <div style="display:flex;justify-content:space-between;"> 
                <button type="submit" class="btn btn-primary mt-3" :disabled="formLoader">
                    {{ formLoader ? "Updating..." : "Update" }}
                </button>
                <button type="button" class="btn btn-danger mt-3 ml-3" v-on:click="logout()">
                    {{ btnLoader ? "Loading..." : "Logout" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';
import VueGoogleAutocomplete from "vue-google-autocomplete";
import axios from 'axios';

export default {
components: { VueGoogleAutocomplete },
  data() {
    return {
      form: {
        name: '',
        email: '',
        address: '',
      },
      formLoader: false,
      btnLoader: false,
    };
  },
  mounted() {
    this.setupAxiosInterceptors();
    this.fetchUserData();
  },
  methods: {
    getAddressData(addressData, placeResultData, id) {
        this.form.address = placeResultData.formatted_address;
    },
    setupAxiosInterceptors() {
      axios.interceptors.request.use((config) => {
        const token = localStorage.getItem('token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      });
    },
    async fetchUserData() {
      try {
        const response = await axios.get('/api/user');
        const { name, email,address } = response.data.user;
        this.form.name = name;
        this.form.email = email;
        this.form.address = address;
      } catch (error) {
        if(error.response.status == 401){
            window.location.replace('/register');
        }
      }
    },
    async updateUser() {
      try {
        this.formLoader = true;
        const response = await axios.put('/api/user', this.form);
        this.formLoader = false;
        this.$swal.fire({
            title: 'Updated Successfully!',
            icon: 'success',
        });
      } catch (error) {
        this.formLoader = false;
        const errorMessages = Object.values(error.response.data.errors).flat();
        this.$swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: errorMessages.join('\n'),
        });
      }
    },
    async logout() {
      try {
        this.btnLoader = true;
        await axios.get('/api/logout');
        this.$swal.fire({
            title: 'Logged Out Successfully!',
            icon: 'success',
        });
        setTimeout(() => {
            window.location.replace('/register');
        }, 1000);
      } catch (error) {
        this.btnLoader = false;
      }
    },
  },
};
</script>
