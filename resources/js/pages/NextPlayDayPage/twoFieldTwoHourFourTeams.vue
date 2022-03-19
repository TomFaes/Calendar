<template>
    <div>
        <h3>
            <button class="btn-info" @click.prevent="previousDay()"><i class="fas fa-angle-double-left"></i></button> 
                {{ calendarData['seasonData']['name'] }}
            <button class="btn-info" @click.prevent="nextDay()"><i class="fas fa-angle-double-right"></i></button>
        </h3>
            
        <div class="container" v-if="calendarData['data'] != undefined">
            <div class="row">
                <div class="col-lg-3 col-1"></div>
                <div class="col-lg-6 col-10">
                    <div style="overflow-x:auto; margin-left:4em;">
                        <table class="table">
                            <thead >
                                <tr>
                                    <th scope="col" style="left:0;">Player</th>
                                    <th scope="col" style="text-align: center;">
                                        {{convertDate(calendarData['data'][calendarData['currentPlayDay']]['day']) }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="groupUser in userData" :key="groupUser.id"> 
                                    <td style="left:0;">{{  groupUser.user_id != null ? groupUser.user.firstname : groupUser.firstname }}</td>
                                    <td v-if="calendarData['data'][calendarData['currentPlayDay']]['user'][groupUser.id]['team'] != ''">
                                        
                                            <span v-if="calendarData['data'][calendarData['currentPlayDay']]['user'][groupUser.id]['team'] == 'team1'">1</span>
                                            <span v-if="calendarData['data'][calendarData['currentPlayDay']]['user'][groupUser.id]['team'] == 'team2'">2</span>
                                            <span v-if="calendarData['data'][calendarData['currentPlayDay']]['user'][groupUser.id]['team'] == 'team3'">3</span>
                                            <span v-if="calendarData['data'][calendarData['currentPlayDay']]['user'][groupUser.id]['team'] == 'team4'">4</span>
                                    </td>
                                     <td v-else :class="getBackground(groupUser.id, calendarData['data'][calendarData['currentPlayDay']]['day'])"></td>
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
import doubleHourMixin from '../../components/mixins/nextDay/doubleHour.js';
    
    export default {
        //this mixin will have all methods needed to be used within this view
        mixins: [doubleHourMixin], 
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