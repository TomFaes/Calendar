<template>
    <div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div style="overflow-x:auto; margin-left:4em;">
                        <table class="table">
                            <thead >
                                <tr>
                                    <th scope="col" style="position:absolute; width:3em; left:0;">Player</th>
                                    <th v-for="data in calendarData['date']"  :key="data.id" scope="col" style="text-align: center;">
                                        {{convertDate(data.date)}}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in userData" :key="user.id">
                                    <td style="position:absolute; width:3em; left:0;">{{ user.firstname }}</td>
                                    <template v-for="data in calendarData['date']">
                                            <td v-if="data[user.id] == 'team1'" :key="data.id"><center>1</center></td>
                                            <td v-else-if="data[user.id] == 'team2'" :key="data.id"><center>2</center></td>
                                            <td v-else-if="data[user.id] == 'team3'" :key="data.id"><center>3</center></td>
                                            <td v-else :class="getBackground(user.id, data.date)" :key="data.id" ></td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import apiCall from '../../services/ApiCall.js';
    import Moment from 'moment';

    export default {
        components: {
            Moment,
        },

        data () {
            return {
                display: "",
            }
        },

        props: {
            'seasonData': {},
            'calendarData': {},
            'userData': {},
            'absenceData': {},
         },

        methods: {
            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MMM');
            },

            convertToDatabaseDate(value){
                return Moment(value, "YYYY-MM-DD").format('YYYYMMDD');
            },

            getBackground(userId, date){
                var colorClass = "free";
                if(this.absenceData[userId] != undefined){
                    for(var i = 0; i < this.absenceData[userId]['date'].length; i++){
                        if(date ==  this.absenceData[userId]['date'][i]){
                            colorClass = "absence";
                        }
                    }                   
                }
                 return colorClass;
             },
        },

        mounted(){

        }
    }
</script>


<style scoped>
.absence{
    background-color: red;
}

.free{
    background-color: #343a40;
}
</style>
