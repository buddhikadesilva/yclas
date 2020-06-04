$(function (){
// Instance the tour
    var tour = new Tour({
        container: "#page-wrapper"
    });

    tour.addSteps([
        {
           element: "#page-welcome",
           title: getTourLocalization("step1_title"),
           content: getTourLocalization("step1_content"),
           path: getTourBasePath() + "oc-panel",
           placement: "top",
           redirect: false,
        },
        {
           element: "#quick-creator-btn",
           title: "",
           content: getTourLocalization("step2_content"),
           path: getTourBasePath() + "oc-panel/category",
           placement: "top",
           redirect: true,
        },
        {
           element: "#page-general-configuration",
           title: "",
           content: getTourLocalization("step3_content"),
           path: getTourBasePath() + "oc-panel/settings/general",
           placement: "top",
           redirect: true,
        },
        {
           element: "#page-email-settings",
           title: "",
           content: getTourLocalization("step11_content"),
           path: getTourBasePath() + "oc-panel/settings/email",
           placement: "top",
           redirect: true,
        },
    ]);

    // Initialize the tour
    tour.init();

    // Start the tour
    tour.start();
});
