<template>
    <div>
        <h3>
            <button class="btn-info" @click.prevent="previousDay()"><i class="fas fa-angle-double-left"></i></button> 
        {{ calendarData['seasonName'] }}
            <button class="btn-info" @click.prevent="nextDay()"><i class="fas fa-angle-double-right"></i></button>
        </h3>
            
        <div class="container" v-if="calendarData['date'] != undefined">
            <div class="row">
                <div class="col-lg-3 col-1"></div>
                <div class="col-lg-6 col-10">
                    <div style="overflow-x:auto; margin-left:4em;">
                        <table class="table">
                            <thead >
                                <tr>
                                    <th scope="col" style="position:absolute; left:0;">Player</th>
                                    <th scope="col" style="text-align: center;">
                                        {{convertDate(calendarData['day'][calendarData['currentPlayDay']]['day']) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in userData" :key="user.id">
                                    <td style="position:absolute; left:0;">{{ user.firstname }}</td>
                                    <td v-if="calendarData['day'][calendarData['currentPlayDay']][user.id] == 'team1'" ><center>1</center></td>
                                    <td v-else-if="calendarData['day'][calendarData['currentPlayDay']][user.id] == 'team2'" ><center>2</center></td>
                                    <td v-else-if="calendarData['day'][calendarData['currentPlayDay']][user.id] == 'team3'" ><center>3</center></td>
                                    <td v-else :class="getBackground(user.id, calendarData['day'][calendarData['currentPlayDay']]['day'])" ></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-3 col-1"></div>
            </div>
        </div>
    </div>   
</template>

<script>
    import Moment from 'moment';

    export default {
        components: {
            Moment,
        },

        data () {
            return {
                
            }
        },

        props: {
                calendarData: {},
                userData: {},
                absenceData: {},
         },

        methods: {
            nextDay(){
                if((this.calendarData['currentPlayDay']  + 1) <  this.calendarData['day'].length ){
                    this.calendarData['currentPlayDay']++;
                }
            },

            previousDay(){
                if((this.calendarData['currentPlayDay'] )  > 0 ){
                    this.calendarData['currentPlayDay']--;
                }
            },

            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MMM');
            },

            getBackground(userId, date){
                var colorClass = "free";
                if(this.absenceData != undefined){
                    if(this.absenceData[userId] != undefined){
                        for(var i = 0; i < this.absenceData[userId]['date'].length; i++){
                            if(date ==  this.absenceData[userId]['date'][i]){
                                colorClass = "absence";
                            }
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

td {
    height: 50px;
    width: 50%;
}
</style>