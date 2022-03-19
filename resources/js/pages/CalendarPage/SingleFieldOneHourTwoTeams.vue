<template>
    <div>
        <div v-if="editCalendarData['data'] != undefined">
            <h1 v-if= "generate == false">
                <button class="btn btn-primary" @click.prevent="editCalendar" v-if="showEditCalenderButton()"><i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                <button v-if="calendarData['seasonData']['allow_replacement'] == 1" class="btn btn-warning" @click.prevent="askReplacement"><i class="fas fa-exchange-alt" style="heigth:14px; width:14px"></i></button>
            </h1>

            <global-layout sizeForm="xlarge">
                <div style="overflow-x:auto; margin-left:4em;">
                    <table class="table">
                        <thead >
                            <tr>
                                <th scope="col" style="width:3em; left:0;">Player</th>
                                <th v-for="date in editCalendarData['data']"  :key="date.id" scope="col" style="text-align: center;">
                                    {{convertDate(date.day)}}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="groupUser in userData" :key="groupUser.id">
                                <td style="width:3em; left:0;">{{  groupUser.user_id != null ? groupUser.user.firstname : groupUser.firstname }}</td>
                                 <template v-for="(data, index) in editCalendarData['data']" >
                                      <!-- view when editing a season -->
                                    <td :class="getBackground(groupUser.id, data.day, data['user'][groupUser.id]['team'])" :key="data.id" @click.prevent="editTeam(index, groupUser.id)" v-if="edit == true">
                                        {{ data['user'][groupUser.id]['team'].replace('team', '') }}
                                        <span v-if="showUpdateButtons(index, groupUser.id)">
                                            <button v-for="button in updateButtons" :key=button class="btn btn-secondary" style="width:100%" @click.stop="updateTeam(index, groupUser.id, button, data['user'][groupUser.id]['teamId'])">{{button}}</button>
                                        </span>
                                    </td>
                                    <!-- view for generating the season -->
                                    <td :class="getBackground(groupUser.id, data.day, '')" :key="data.id" v-else-if="edit == false && data['user'][groupUser.id] == undefined"></td>
                                    <!-- view to replace or cancel replacement -->
                                    <td :class="getBackground(groupUser.id, data.day, '', 'replacement')" :key="data.id" v-if="edit == false && data['user'][groupUser.id]['replacement'] == 1">
                                        {{ data['user'][groupUser.id]['team'].replace('team', '') }}
                                        <span v-if="groupUser.user_id == loggedInUser.id  && replacement == true">
                                            <button class="btn btn-warning" @click.stop="cancelRequestForReplacement(groupUser.id, data['user'][groupUser.id]['teamId'])">Vervanging annuleren</button>
                                        </span>
                                        <span v-if="data['user'][getLoggedInGroupUserId]['teamId'] == ''">
                                            <button class="btn btn-warning" @click.stop="confirmReplacement(groupUser.id, data['user'][groupUser.id]['teamId'])"><i class="fas fa-exchange-alt" style="heigth:14px; width:14px"></i></button>
                                        </span>
                                    </td>
                                    <!-- default view for the season -->
                                    <td :class="getBackground(groupUser.id, data.day, data['user'][groupUser.id]['team'])" :key="data.id" v-else-if="edit == false">
                                        {{ data['user'][groupUser.id]['team'].replace('team', '') }}
                                        <span v-if="groupUser.user_id == loggedInUser.id && data['user'][groupUser.id]['teamId'] != '' && compareDates(data.day) && replacement == true">
                                            <button class="btn btn-warning" @click.stop="askForReplacement(groupUser.id, data['user'][groupUser.id]['teamId'])">Vervanging gezocht</button>
                                        </span>
                                    </td>
                                 </template>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div  v-if="edit == true"><button class="btn btn-primary" @click.prevent="saveTeams()"> Save</button></div>
            </global-layout>
        </div>

        <br><hr><br>

         <div v-if="calendarData['stats'] != undefined">
            <global-layout sizeForm="xlarge">
                <span class="absence">Kan niet spelen</span><br>
                <span class="free" style="color: white">Beschikbaar voor eventuele vervanging</span><br>
                <span class="replacement">Zoekt naar vervanging</span><br>
            </global-layout>
            <br><hr><br>

           <global-layout sizeForm="xlarge">
                <div style="overflow-x:auto; margin-left:4em;">
                    <table class="table">
                        <thead >
                            <tr>
                                <th scope="col">Naam</th>
                                <th scope="col">Ploeg 1</th>
                                <th scope="col">Ploeg 2</th>
                                <th scope="col">Totaal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in userData" :key="user.id">
                                <td style="width:3em; left:0;">{{ user.firstname }}</td>
                                <td><span v-if="calendarData['stats'][user.id]">{{ calendarData['stats'][user.id]['team1'] }}</span></td>
                                <td><span v-if="calendarData['stats'][user.id]">{{ calendarData['stats'][user.id]['team2'] }}</span></td>
                                <td><span v-if="calendarData['stats'][user.id]">{{ calendarData['stats'][user.id]['total'] }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
           </global-layout>
        </div>
    </div>
</template>

<script>
    import doubleHourMixin from '../../components/mixins/generator/doubleHour.js';
    
    export default {
        components: {

        },

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

    .replacement {
        background-color: yellow;
    }

    td:hover {
        background-color: lightgray;
    }

    td, th {
        text-align: center;
    }
</style>