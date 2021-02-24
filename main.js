require(['jquery', 'jqueryui'], function ($) {

    $(function() {
        $(".carousel").on('slide.bs.carousel', function () {

            $(".miro_video_player_iframe").contents().find("video").each(function () {
                this.currentTime = 0;
                this.pause();
            });

            $('.miro_video_player_iframe').each(function () {
                var url = this.src;

                if(url.indexOf('youtube') !== -1)
                    this.contentWindow.postMessage('{"event":"command","func":"stopVideo","args":""}', '*');

                if(url.indexOf('dailymotion') !== -1)
                    this.contentWindow.postMessage('{"command":"pause","parameters":[]}', "*");

                if(url.indexOf('vimeo') !== -1)
                    this.contentWindow.postMessage('{"method":"pause","value":null}', "*");

                if(url.indexOf('upvdstream') !== -1) {
                    this.contentWindow.postMessage('{"command":"pause"}', "*");
                }
            });
        })
    });
});