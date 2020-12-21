
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
        },

    methods: {
        nextDay(){
            if((this.calendarData['currentPlayDay']  + 1) <  this.calendarData['data'].length ){
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

        getBackground(groupUserId, date){
            var colorClass = "free";
            if(this.calendarData['absenceData'] != undefined){
                if(this.calendarData['absenceData'][groupUserId] != undefined){
                    for(var i = 0; i < this.calendarData['absenceData'][groupUserId]['date'].length; i++){
                        if(date ==  this.calendarData['absenceData'][groupUserId]['date'][i]){
                            colorClass = "absence";
                        }
                    }   
                }
            }
                return colorClass;
            },
    },
}