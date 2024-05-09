<script>

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'


export default {
    name: "App",
    components: {VueDatePicker},
    data() {
        return {
            workDays: {
                date: null,
                settings: 1,
                result: null,
                errors: {},
                message: null
            },
            ticket: {
                start: null,
                estimate: null,
                workdays: true,
                settings: 1,
                result: null,
                errors: {},
                message: null,
                hoursStart: {
                    hours: 8,
                    minutes: 0
                },
                hoursEnd: {
                    hours: 16,
                    minutes: 0
                }
            }
        }
    },
    computed: {
        formatedDate() {
            if (this.workDays.date === null) {
                return null;
            }

            return this.workDays.date.toLocaleDateString();
        },

        formatedTicketStart() {
            if (this.ticket.start === null) {
                return null;
            }

            let date = this.ticket.start.toLocaleDateString();
            let hours = this.ticket.start.getHours().toString().padStart(2, '0');
            let minutes = this.ticket.start.getMinutes().toString().padStart(2, '0');

            return `${date} ${hours}:${minutes}`;
        }
    },
    methods: {

        formOneClearDate() {
            this.workDays.date = null;
        },

        formOneClearForm() {
            this.workDays.result = null;
            this.workDays.message = null;
            this.workDays.errors = {};
        },

        formOneProcessResponse(response) {
            if (response.data.isWorkDay) {
                this.workDays.result = 'pracovní den';
                return;
            }

            this.workDays.result = 'den pracovního klidu';
        },

        formOneProcessError(error) {
            if (error.response.status === 422) {
                this.workDays.errors = error.response.data.errors;
                this.workDays.message = error.response.data.message;
                return;
            }

            this.workDays.message = 'Chyba aplikace';
        },

        formOneCheckDate() {
            if (this.formatedDate === null) {
                this.formOneClearForm();
                this.formOneClearDate();
                this.workDays.errors = {date: ['Date field is required']};
                return;
            }

            const date = this.workDays.date.toISOString().split('T')[0];
            const data = {date: date, settings: this.workDays.settings};
            this.formOneClearForm();

            window.axios.post('/api/date/check', data)
                .then(this.formOneProcessResponse)
                .catch(this.formOneProcessError)
        },


        formTwoClearForm() {
            this.ticket.message = null;
            this.ticket.result = null;
            this.ticket.errors = {};
        },

        formTwoProcessResponse(response) {
            this.ticket.result = response.data.endTime;
        },

        formTwoProcessError(error) {
            if (error.response.status === 422) {
                this.ticket.errors = error.response.data.errors;
                this.ticket.message = error.response.data.message;
                return;
            }

            this.ticket.message = 'Chyba aplikace';
        },

        formTwoCalcTime() {
            let start = null;

            if (this.ticket.start !== null) {
                const date = this.ticket.start.toISOString().split('T')[0];
                const hours = this.ticket.start.getHours().toString().padStart(2, '0');
                const minutes = this.ticket.start.getMinutes().toString().padStart(2, '0');
                start = `${date} ${hours}:${minutes}`;
            }

            const data = {
                estimate: this.ticket.estimate,
                workdays: this.ticket.workdays,
                settings: this.ticket.settings,
                hoursStart: this.ticket.hoursStart,
                hoursEnd: this.ticket.hoursEnd,
                start: start,
            };

            this.formTwoClearForm();

            window.axios.post('/api/ticket/calc', data)
                .then(this.formTwoProcessResponse)
                .catch(this.formTwoProcessError)
        }
    }
}
</script>

<template>
    <main class="container">
        <h2>General 1-1</h2>

        <div v-if="workDays.message !== null" class="alert alert-danger mb-4" role="alert" v-html="workDays.message">
        </div>

        <div class="row">
            <div class="col-6">
                <div class="my-2">
                    <label class="mb-2">Date:</label>
                    <VueDatePicker
                        v-model="workDays.date"
                        @cleared="formOneClearDate"
                        locale="cs"
                        :enable-time-picker="false"
                        auto-apply
                        :state="workDays.errors.hasOwnProperty('date') ? false : null"
                        :class="{'is-invalid': workDays.errors.hasOwnProperty('date')}"
                        :format="formatedDate"
                    />
                    <div v-if="workDays.errors.hasOwnProperty('date')" class="invalid-feedback"
                         v-html="workDays.errors.date.join('<br>')"></div>
                </div>

                <div class="my-2">
                    <label class="mb-2">Settings:</label>
                    <select class="form-select" v-model="workDays.settings"
                            :class="{'is-invalid': workDays.errors.hasOwnProperty('settings')}">
                        <option value="1">CZ</option>
                        <option value="2">SK</option>
                        <option value="0">Vlastní</option>
                    </select>
                    <div v-if="workDays.errors.hasOwnProperty('settings')" class="invalid-feedback"
                         v-html="workDays.errors.settings.join('<br>')"></div>
                </div>

                <div class="my-2 py-3">
                    <button class="btn btn-sm btn-success" @click="formOneCheckDate">Check date</button>
                </div>

            </div>

            <div class="col-6">
                <label class="mb-2">Result:</label>
                <div class="mt-2">{{ workDays.result }}</div>
            </div>
        </div>

        <hr class="my-4">

        <h2>General 1-2</h2>

        <div v-if="ticket.message !== null" class="alert alert-danger mb-4" role="alert" v-html="ticket.message">
        </div>

        <div class="row">
            <div class="col-6">
                <div class="my-2">
                    <label class="mb-2">Start date:</label>
                    <VueDatePicker
                        v-model="ticket.start"
                        :clearable="false"
                        locale="cs"
                        auto-apply
                        :state="ticket.errors.hasOwnProperty('start') ? false : null"
                        :class="{'is-invalid': ticket.errors.hasOwnProperty('start')}"
                        :format="formatedTicketStart"
                    />
                    <div v-if="ticket.errors.hasOwnProperty('start')" class="invalid-feedback"
                         v-html="ticket.errors.start.join('<br>')"></div>
                </div>

                <div class="my-2">
                    <label class="mb-2">Estimate:</label>
                    <div class="input-group mb-3" :class="{'has-validation': ticket.errors.hasOwnProperty('estimate')}">
                        <input type="number" min="0" class="form-control" v-model="ticket.estimate"
                               :class="{'is-invalid': ticket.errors.hasOwnProperty('estimate')}">
                        <span class="input-group-text">minutes</span>
                        <div v-if="ticket.errors.hasOwnProperty('estimate')" class="invalid-feedback"
                             v-html="ticket.errors.estimate.join('<br>')"></div>
                    </div>

                </div>

                <div class="my-2">
                    <label class="mb-2">Only work days</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" v-model="ticket.workdays"
                               :class="{'is-invalid': ticket.errors.hasOwnProperty('workdays')}">
                        <div v-if="ticket.errors.hasOwnProperty('workdays')" class="invalid-feedback"
                             v-html="ticket.errors.workdays.join('<br>')"></div>
                    </div>
                </div>

                <div class="my-2" v-if="ticket.workdays">
                    <label class="mb-2">Work days settings:</label>
                    <select class="form-select" v-model="ticket.settings"
                            :class="{'is-invalid': ticket.errors.hasOwnProperty('settings')}">
                        <option value="1">CZ</option>
                        <option value="2">SK</option>
                        <option value="3">Vlastní</option>
                    </select>
                    <div v-if="ticket.errors.hasOwnProperty('settings')" class="invalid-feedback"
                         v-html="ticket.errors.settings.join('<br>')"></div>
                </div>

                <div class="my-2">
                    <label class="mb-2">Work day start at</label>
                    <VueDatePicker
                        v-model="ticket.hoursStart"
                        time-picker
                        :clearable="false"
                        :state="ticket.errors.hasOwnProperty('hoursStart') ? false : null"
                        :class="{'is-invalid': ticket.errors.hasOwnProperty('hoursStart')}"
                    />
                    <div v-if="ticket.errors.hasOwnProperty('hoursStart')" class="invalid-feedback"
                         v-html="ticket.errors.hoursStart.join('<br>')"></div>
                </div>

                <div class="my-2">
                    <label class="mb-2">Work day end at</label>
                    <VueDatePicker
                        v-model="ticket.hoursEnd"
                        time-picker
                        :clearable="false"
                        :state="ticket.errors.hasOwnProperty('hoursEnd') ? false : null"
                        :class="{'is-invalid': ticket.errors.hasOwnProperty('hoursEnd')}"
                    />
                    <div v-if="ticket.errors.hasOwnProperty('hoursEnd')" class="invalid-feedback"
                         v-html="ticket.errors.hoursEnd.join('<br>')"></div>
                </div>

                <div class="my-2 py-3">
                    <button class="btn btn-sm btn-success" @click="formTwoCalcTime">Calc ticket end date</button>
                </div>
            </div>

            <div class="col-6">
                <label class="mb-2">Result:</label>
                <div>{{ ticket.result }}</div>
            </div>

        </div>
    </main>
</template>

<style scoped>

</style>
