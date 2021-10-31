<template>
    <div id="home" class="p-6">
        <div class="lg:flex justify-between items-center mb-6">
          <p class="text-2xl font-semibold mb-2 lg:mb-0">Hello {{getAuth.user ?  getAuth.user.name : 'Unknown'}}</p>
          <!-- <button class="bg-blue-500 hover:bg-blue-600 focus:outline-none rounded-lg px-6 py-2 text-white font-semibold shadow">View Logs</button> -->
        </div>

        <div class="flex flex-wrap -mx-3 mb-20">
            <p>
              Select Tasks on the sidebar. You can switch the sidebar on or off using Hamburger button. 
            </p>
            <p>
              This is the beta version of system. Contact me (Dion) regarding any issue. 
            </p>
        </div>       
    </div>
</template> 

<script>
// import Chart from 'chart.js'
import {mapGetters} from 'vuex'

export default {
    name: 'DashboardHome',
    
    data() {
    
        return {
            buyersData: {
                type: 'line',
                data: {
                    labels : ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
                    datasets:[{
                        backgroundColor : "rgba(99,179,237,0.4)",
                        strokeColor : "#63b3ed",
                        pointColor : "#fff",
                        pointStrokeColor : "#63b3ed",
                        data : [203,156,99,251,305,247,256]
                    },
                    {
                        backgroundColor : "rgba(198,198,198,0.4)",
                        strokeColor : "#f7fafc",
                        pointColor : "#fff",
                        pointStrokeColor : "#f7fafc",
                        data : [86,97,144,114,94,108,156]
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display:false
                            },  
                            ticks: {
                                display: false
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            },
            reviewsData: {
                type: 'bar',
                data: {
                    labels : ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
                    datasets:[{
                        backgroundColor : "rgba(99,179,237,0.4)",
                        strokeColor : "#63b3ed",
                        pointColor : "#fff",
                        pointStrokeColor : "#63b3ed",
                        data : [203,156,99,251,305,247,256]
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            gridLines: {
                                display:false
                            },  
                            ticks: {
                                display: false
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }

            }
        }
    },

    computed: {
        ...mapGetters({
          getAuth: 'auth/getAuth'
        }),
    },
    mounted () {
        // new Chart(document.getElementById('buyers-chart'), this.buyersData)
        // new Chart(document.getElementById('reviews-chart'), this.reviewsData)
    }
}
</script>