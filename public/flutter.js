function close_webview_back_to_dashboard() {
    //alert('Attempting to close WebView and return to dashboard');
    // 'WebMessage' is the name we will define in FlutterFlow
    if (window.WebMessage) {
        //alert('Closing WebView and returning to dashboard');
        window.WebMessage.postMessage('close_webview_back_to_dashboard');
    }
}
function onHardwareBackPress() {
    console.log("Back button detected by Flutter!");
    close_webview_back_to_dashboard();
}