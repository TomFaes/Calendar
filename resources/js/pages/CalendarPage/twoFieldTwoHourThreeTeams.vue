<template>
    <div>
        <div v-if="editCalendarData['data'] != undefined">
            <h1 v-if= "generate == false">
                {{  calendarData['seasonData']['name'] }}
                <button class="btn btn-primary" @click.prevent="editCalendar" v-if="showEditCalenderButton()"><i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
                <button class="btn btn-warning" @click.prevent="askReplacement"><i class="fas fa-exchange-alt" style="heigth:14px; width:14px"></i></button>
            </h1>

            <global-layout sizeForm="xlarge">
                <div style="overflow-x:auto; margin-left:4em;">
                    <table class="table">
                        <thead >
                            <tr>
                                <th scope="col" style="position:absolute; width:3em; left:0;">Player</th>
                                <th v-for="date in editCalendarData['data']"  :key="date.id" scope="col" style="text-align: center;">
                                    {{convertDate(date.day)}}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="groupUser in userData" :key="groupUser.id">
                                <td style="position:absolute; width:3em; left:0;">{{  groupUser.user_id != null ? groupUser.user.firstname : groupUser.firstname }}</td>
                                 <template v-for="(data, index) in editCalendarData['data']" >
                                      <!-- view when editing a season -->
                                    <td :class="getBackground(groupUser.id, data.day, data['user'][groupUser.id]['team'])" :key="data.id" @click.prevent="editTeam(index, groupUser.id)" v-if="edit == true">
                                        <center>
                                            {{ data['user'][groupUser.id]['team'].replace('team', '') }}
                                        </center>
                                        <span v-if="showUpdateButtons(index, groupUser.id)">
                                            <button v-for="button in updateButtons" :key=button class="btn btn-secondary" style="width:100%" @click.stop="updateTeam(index, groupUser.id, button, data['user'][groupUser.id]['teamId'])">{{button}}</button>
                                        </span>
                                    </td>
                                    <!-- view for generating the season -->
                                    <td :class="getBackground(groupUser.id, data.day, '')" :key="data.id" v-else-if="edit == false && data['user'][groupUser.id] == undefined"></td>
                                    <!-- view to replace or cancel replacement -->
                                    <td :class="getBackground(groupUser.id, data.day, '', 'replacement')" :key="data.id" v-if="edit == false && data['user'][groupUser.id]['replacement'] == 1">
                                        <center>{{ data['user'][groupUser.id]['team'].replace('team', '') }}</center>
                                        <span v-if="groupUser.user_id == loggedInUser.id  && replacement == true">
                                            <button class="btn btn-warning" @click.stop="cancelRequestForReplacement(groupUser.id, data['user'][groupUser.id]['teamId'])">Vervanging annuleren</button>
                                        </span>
                                        <span v-if="data['user'][getLoggedInGroupUserId]['teamId'] == ''">
                                            <button class="btn btn-warning" @click.stop="confirmReplacement(groupUser.id, data['user'][groupUser.id]['teamId'])"><i class="fas fa-exchange-alt" style="heigth:14px; width:14px"></i></button>
                                        </span>
                                    </td>
                                    <!-- default view for the season -->
                                    <td :class="getBackground(groupUser.id, data.day, data['user'][groupUser.id]['team'])" :key="data.id" v-else-if="edit == false">
                                        <center>
                                            {{ data['user'][groupUser.id]['team'].replace('team', '') }}
                                        </center>
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
                <span>1: 1E UUR ENKEL / 2E UUR DUBBEL</span><br>
                <span>2: 2E UUR ENKEL / 1E UUR DUBBEL</span><br>
                <span>3: 2 UUR DUBBEL</span><br>
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
                                <th scope="col" style="position:absolute; width:3em; left:0;">Naam</th>
                                <th scope="col" style="text-align: center;">Tegen</th>
                                <th scope="col" style="text-align: center;">Ploeg 1</th>
                                <th scope="col" style="text-align: center;">Ploeg 2</th>
                                <th scope="col" style="text-align: center;">Ploeg 3</th>
                                <th scope="col" style="text-align: center;">Totaal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in userData" :key="user.id">
                                <td style="position:absolute; width:3em; left:0;">{{ user.firstname }}</td>
                                <td><center>{{ calendarData['stats'][user.id]['countAgainst'] }}</center></td>
                                <td><center>{{ calendarData['stats'][user.id]['team1'] }}</center></td>
                                <td><center>{{ calendarData['stats'][user.id]['team2'] }}</center></td>
                                <td><center>{{ calendarData['stats'][user.id]['team3'] }}</center></td>
                                <td><center>{{ calendarData['stats'][user.id]['total'] }}</center></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
           </global-layout>
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
                edit: false,
                replacement: false,
                editIndex: "",
                editUserId: "",
                editCalendarData: {}, 
                updateButtons: {},
                updateData: {},
                'formData': new FormData(),
                getLoggedInGroupUserId : 0,
            }
        },

        props: {
            'calendarData': {},
            'userData': {},
            'loggedInUser': {},
            'generate': Boolean,
         },

         watch:{
            calendarData (){
                 this.editCalendarData = this.calendarData;
            }
        },

        methods: {
            showEditCalenderButton(){
                if(this.loggedInUser.id != this.editCalendarData['seasonData']['admin_id']){
                    return false;
                }
                return true;
            },

            showUpdateButtons(index, groupUserId){
                if(this.editIndex != index){
                    return false;
                }
                if(this.editUserId != groupUserId){
                    return false;
                }
                if(this.loggedInUser.id != this.editCalendarData['seasonData']['admin_id']){
                    return false;
                }
                return true;
            },

            getLoggedInGroupUser(){
                console.log(this.loggedInUser.id);
                for(var groupUser in this.userData){
                    if(this.loggedInUser.id == this.userData[groupUser]['user_id']){
                        this.getLoggedInGroupUserId =  this.userData[groupUser]['id'];
                    }
                }
            },

            editCalendar(){
                if(this.edit === false){
                    this.edit = true;
                    return;
                }
                this.edit = false;
                return;
            },

            askReplacement(){
                if(this.replacement == false){
                    this.replacement = true;
                    return true;
                }
                this.replacement = false;
                return;
            },

            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MMM');
            },

            compareDates(value){
                var date1 = Moment(value).format('YYYYMMDD');;
                var date2 = Moment().format('YYYYMMDD');;
                if(date1 <  date2){
                    return false;
                }
                return true;
            },

            getBackground(groupUserId, date, team, replacement = ""){
                var colorClass = "";
                if(team == ""){
                    colorClass = "free";
                }

                if(replacement == "replacement"){
                    return "replacement"
                }

                if(this.calendarData['absenceData'] == undefined){
                    return colorClass;
                }
                if(this.calendarData['absenceData'][groupUserId] == undefined){
                    return colorClass;
                }

                for(var i = 0; i < this.calendarData['absenceData'][groupUserId]['date'].length; i++){
                    if(date ==  this.calendarData['absenceData'][groupUserId]['date'][i]){
                        colorClass = "absence";
                    }
                }
                 return colorClass;
             },

             editTeam(index, groupUserId){
                 if(index == this.editIndex && groupUserId == this.editUserId){
                    this.editIndex = "";
                    this.editUserId = "";
                 }else{
                    this.editIndex = index;
                    this.editUserId = groupUserId;
                 }
                 
                 this.updateButtons = {};

                 this.updateButtons[0] = 'Free';
                 this.updateButtons[1] = "Absence";

                var x = 2;
                 for(var calendar in this.editCalendarData['data'][index]['teams']){
                     if(this.calendarData.data[index].user[groupUserId].team == this.editCalendarData['data'][index]['teams'][calendar]['team']){
                         continue;
                     }

                     if(this.editCalendarData['data'][index]['teams'][calendar]['groupUserId'] !== null){
                        continue;
                     }
                     var doubleCheck = false;
                     for(var check in this.updateButtons){
                         if(this.updateButtons[check] == this.editCalendarData['data'][index]['teams'][calendar]['team']){
                             doubleCheck = true;
                         }
                     }
                     if(doubleCheck == true){
                         continue;
                     }

                    this.updateButtons[x] = this.editCalendarData['data'][index]['teams'][calendar]['team'];
                    x++;
                 }
             },

             updateTeam(index, userId, buttonChoice, currentTeamId = 0){
                 if(currentTeamId > 0){
                    this.updateData[currentTeamId] = "";
                 }
                 
                 if(buttonChoice == "Free" || buttonChoice == 'Absence'){
                    if(currentTeamId == 0){
                        this.editIndex = "";
                        this.editUserId = "";
                        return;
                    }
                    
                    this.editCalendarData['data'][index]['teams'][currentTeamId]['groupUserId'] = null;
                    this.editCalendarData['data'][index]['user'][userId]['team'] = "";
                    this.editCalendarData['data'][index]['user'][userId]['teamId'] = "";
                    
                    this.editIndex = "";
                    this.editUserId = "";
                    return;
                 }
                
                 for(var teamId in this.editCalendarData['data'][index]['teams']){
                     if(this.editCalendarData['data'][index]['teams'][teamId]['team'] === buttonChoice && this.editCalendarData['data'][index]['teams'][teamId]['groupUserId'] === null){
                        this.editCalendarData['data'][index]['teams'][teamId]['groupUserId'] = userId;
                        this.editCalendarData['data'][index]['user'][userId]['team'] = buttonChoice;
                        this.editCalendarData['data'][index]['user'][userId]['teamId'] = teamId;
                        this.updateData[teamId] = userId;
                        
                        if(currentTeamId > 0){
                            this.editCalendarData['data'][index]['teams'][currentTeamId]['groupUserId'] = null;
                            if(buttonChoice == "Free" || buttonChoice == 'Absence'){
                                this.editCalendarData['data'][index]['user'][userId]['team'] = "";
                                this.editCalendarData['data'][index]['user'][userId]['teamId'] = "";
                            }
                        }
                        break;
                     }
                }
                this.editIndex = "";
                this.editUserId = "";
             },

             saveTeams(){
                this.formData.append('teamRange', JSON.stringify(this.updateData));
                apiCall.postData('team/range', this.formData)
                .then(response =>{
                    this.message = "Teams are updated";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$bus.$emit('reloadCalendar');
                    this.edit = false;
                }).catch(error => {
                    this.errors = error;
                });
             },

             askForReplacement(groupUser, teamId){
                apiCall.postData('team/' + teamId + '/askForReplacement')
                .then(response =>{
                    this.message = "Vervangingsaanvraag is aanvaard";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$bus.$emit('reloadCalendar');
                }).catch(error => {
                    this.errors = error;
                });
             },

             cancelRequestForReplacement(groupUser, teamId){
                apiCall.postData('team/' + teamId + '/cancelRequestForReplacement')
                .then(response =>{
                    this.message = "Vervanging is geannulleerd";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$bus.$emit('reloadCalendar');
                }).catch(error => {
                    this.errors = error;
                });
             }, 

             confirmReplacement(groupUser, teamId){
                apiCall.postData('team/' + teamId + '/confirmReplacement')
                .then(response =>{
                    this.message = "Vervanging aanvaard";
                    this.$bus.$emit('showMessage', this.message,  'green', '2000' );
                    this.$bus.$emit('reloadCalendar');
                }).catch(error => {
                    this.errors = error;
                });
             },
        },

        mounted(){
            this.editCalendarData = this.calendarData;
            this.getLoggedInGroupUser();
        },
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
</style>