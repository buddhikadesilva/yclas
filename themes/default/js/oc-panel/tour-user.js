$(function (){
// Instance the tour
    var tour = new Tour({
        container: "#content"
    });

    tour.addSteps([
        {
           element: "#page-my-dvertisements",
           title: "",
           content: getTourLocalization("step7_content"),
           path: getTourBasePath() + "oc-panel/profile/ads",
           placement: "top",
           redirect: false,
        },
        {
           element: "#page-edit-profile",
           title: "",
           content: getTourLocalization("step8_content"),
           path: getTourBasePath() + "oc-panel/profile/edit",
           placement: "top",
           redirect: true,
        },
        {
           element: "#menu-profile-options",
           title: "",
           content: getTourLocalization("step9_content"),
           path: getTourBasePath() + "oc-panel/profile/edit",
           placement: "right",
           redirect: true,
        },
        {
           element: "#visit-website",
           title: "",
           content: getTourLocalization("step10_content"),
           path: getTourBasePath() + "oc-panel/profile/edit",
           placement: "left",
           redirect: true,
    }
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
});