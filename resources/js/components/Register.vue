<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                Subscribe
            </div>
          <div class="card-body">
            <form @submit.prevent="registerUser">
              <div class="form-group">
                <label for="name">Name</label>
                <input v-model="form.name" type="text" class="form-control" id="name" placeholder="Name" />
              </div>
              <div class="form-group">
                <label for="email">Email</label>
                <input v-model="form.email" type="email" class="form-control" id="email" placeholder="Email"  autocomplete="new-password"/>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input v-model="form.password" type="password" class="form-control" id="password" placeholder="Password" autocomplete="new-password"/>
              </div>
              <div class="form-group">
                <label for="name">Address</label>
                <vue-google-autocomplete id="map" classname="form-control" placeholder="Address" v-on:placechanged="getAddressData"></vue-google-autocomplete>
              </div>
               <div class="form-group mt-3">
                    <label for="card-element">Card Information</label>
                    <div id="card-element" class="form-control">
                    </div>
                </div>
              <button type="submit" class="btn btn-primary mt-3" :disabled="this.formLoader">{{ this.formLoader ? "Loading..." : "Register" }}</button>
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
import { loadStripe } from '@stripe/stripe-js';

const stripePromise = loadStripe(import.meta.env.VITE_APP_STRIPE_KEY);

export default {
components: { VueGoogleAutocomplete },
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        address: '',
      },
      cardElement: null, 
      formLoader: false,
    };
  },
  methods: {
    getAddressData(addressData, placeResultData, id) {
        this.form.address = placeResultData.formatted_address;
    },
    async renderCard() {
      try {
         const stripe = await stripePromise;
        const elements = stripe.elements();

        this.cardElement = elements.create('card');
        this.cardElement.mount('#card-element');
      } catch (error) {
      }
    },
    async registerUser() {
      try {
        this.formLoader = true;
         const stripe = await stripePromise;
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: this.cardElement,
            });

            if (error) {
                this.formLoader = false;
                return;
            }

            const response = await axios.post('/api/register', {
                ...this.form,
                payment_method: paymentMethod.id,
            });
            this.formLoader = false;
            localStorage.setItem('token',response.data.token.plainTextToken);
            this.$swal.fire({
                title: 'Registered!',
                icon: 'success',
            });
            setTimeout(() => {
                window.location.replace('/dashboard');
            }, 1000);
      } catch (error) {
        this.formLoader = false;
        const errorMessages = Object.values(error.response.data.errors).flat();
        this.$swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: errorMessages.join('\n'),
        });
        return;
      }
    },
  },
  mounted() { 
    this.renderCard();
  },
};
</script>
