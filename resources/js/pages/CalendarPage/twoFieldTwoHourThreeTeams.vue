<template>
    <div>
        <div v-if="editCalendarData['data'] != undefined">
            <h1 v-if= "generate == false">
                {{  calendarData['seasonData']['name'] }}
                <button class="btn btn-primary" @click.prevent="editCalendar" v-if="loggedInUser.id == editCalendarData['seasonData']['admin_id']"><i class="fa fa-edit" style="heigth:14px; width:14px"></i></button>
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
                            <tr v-for="user in userData" :key="user.id">
                                <td style="position:absolute; width:3em; left:0;">{{ user.firstname }}</td>
                                 <template v-for="(data, index) in editCalendarData['data']" >
                                    <td :class="getBackground(user.id, data.day, data['user'][user.id]['team'])" :key="data.id" @click.prevent="editTeam(index, user.id)" v-if="edit == true">
                                        <center v-if="data['user'][user.id]['team'] == 'team1'">1</center>
                                        <center v-if="data['user'][user.id]['team'] == 'team2'">2</center>
                                        <center v-if="data['user'][user.id]['team'] == 'team3'">3</center>
                                        <span v-if="editIndex == index && editUserId == user.id && loggedInUser.id == editCalendarData['seasonData']['admin_id']">
                                            <button v-for="button in updateButtons" :key=button class="btn btn-secondary" style="width:100%" @click.stop="updateTeam(index, user.id, button, data['user'][user.id]['teamId'])">{{button}}</button>
                                        </span>
                                    </td>
                                    <td :class="getBackground(user.id, data.day, data['user'][user.id]['team'])" :key="data.id" v-if="edit == false">
                                        <center v-if="data['user'][user.id]['team'] == 'team1'">1</center>
                                        <center v-if="data['user'][user.id]['team'] == 'team2'">2</center>
                                        <center v-if="data['user'][user.id]['team'] == 'team3'">3</center>
                                    </td>
                                 </template>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div  v-if="edit == true"><button class="btn btn-primary" @click.prevent="saveTeams()"> Save</button></div>
            </global-layout>
        </div>

        <br>

         <div v-if="calendarData['stats'] != undefined">
            <global-layout sizeForm="xlarge">
                <span>1: 1E UUR ENKEL / 2E UUR DUBBEL</span><br>
                <span>2: 2E UUR ENKEL / 1E UUR DUBBEL</span><br>
                <span>3: 2 UUR DUBBEL</span><br>
                <span class="absence">Kan niet spelen</span><br>
                <span class="free" style="color: white">Beschikbaar voor eventuele vervanging</span><br>
            </global-layout>

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
                display: "",
                editIndex: "",
                editUserId: "",
                editCalendarData: {}, 
                updateButtons: {},
                updateData: {},
                'formData': new FormData(),
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
            editCalendar(){
                if(this.edit === false){
                    this.edit = true;
                    return;
                }
                this.edit = false;
                return;
            },

            convertDate(value){
                return Moment(value, "YYYY-MM-DD").format('DD/MMM');
            },

            convertToDatabaseDate(value){
                return Moment(value, "YYYY-MM-DD").format('YYYYMMDD');
            },

            getBackground(userId, date, team){
                var colorClass = "";
                if(team == ""){
                    colorClass = "free";
                }               

                if(this.calendarData['absenceData'] == undefined){
                    return colorClass;
                }
                if(this.calendarData['absenceData'][userId] == undefined){
                    return colorClass;
                }

                for(var i = 0; i < this.calendarData['absenceData'][userId]['date'].length; i++){
                    if(date ==  this.calendarData['absenceData'][userId]['date'][i]){
                        colorClass = "absence";
                    }
                }
                 return colorClass;
             },

             editTeam(index, userId){
                 if(index == this.editIndex && userId == this.editUserId){
                    this.editIndex = "";
                    this.editUserId = "";
                 }else{
                    this.editIndex = index;
                    this.editUserId = userId;
                 }
                 
                 this.updateButtons = {};

                 this.updateButtons[0] = 'Free';
                 this.updateButtons[1] = "Absence";

                var x = 2;
                 for(var calendar in this.editCalendarData['data'][index]['teams']){
                     if(this.calendarData.data[index].user[userId].team == this.editCalendarData['data'][index]['teams'][calendar]['team']){
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

                    this.editCalendarData['data'][index]['user'][userId]['team'] = "";
                    this.editCalendarData['data'][index]['user'][userId]['teamId'] = "";
                    
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
             }
        },


        mounted(){
            this.editCalendarData = this.calendarData;
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

td:hover {
    background-color: lightgray;
}
</style>