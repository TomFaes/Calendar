
import apiCall from '../../../services/ApiCall.js';
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
            seasonId: "",
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
            if(this.loggedInUser.id == undefined){
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
            if(team != ''){
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
                this.$store.dispatch('getMessage', {message: this.message});
                this.$store.dispatch('getSelectedCalendar', {id: this.seasonId});
                this.edit = false;
            }).catch(error => {
                this.errors = error;
            });
         },

         askForReplacement(groupUser, teamId){
            apiCall.postData('team/' + teamId + '/ask_for_replacement')
            .then(response =>{
                this.message = "Vervangingsaanvraag is aanvaard";
                this.$store.dispatch('getMessage', {message: this.message});
                this.$store.dispatch('getSelectedCalendar', {id: this.seasonId});
            }).catch(error => {
                this.errors = error;
            });
         },

         cancelRequestForReplacement(groupUser, teamId){
            apiCall.postData('team/' + teamId + '/cancel_request_for_replacement')
            .then(response =>{
                this.message = "Vervanging is geannulleerd";
                this.$store.dispatch('getMessage', {message: this.message});
                this.$store.dispatch('getSelectedCalendar', {id: this.seasonId});
            }).catch(error => {
                this.errors = error;
            });
         }, 

         confirmReplacement(groupUser, teamId){
            apiCall.postData('team/' + teamId + '/confirm_replacement')
            .then(response =>{
                this.message = "Vervanging aanvaard";
                this.$store.dispatch('getMessage', {message: this.message});
                this.$store.dispatch('getSelectedCalendar', {id: this.seasonId});
            }).catch(error => {
                this.errors = error;
            });
         },
    },

    mounted(){
        this.editCalendarData = this.calendarData;
        this.getLoggedInGroupUser();
        this.seasonId = this.calendarData.seasonData.id;
    },
}
