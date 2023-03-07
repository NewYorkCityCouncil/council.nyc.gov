<div class="container hearings-section">
  <div class="row">
    <div class="columns">
      <h2>
        HEARINGS
        <hr style="border-bottom: 1px solid #58595B; margin: 30px 0;"/>
      </h2>
    </div>
  </div>
  <div class="row" id="hearings">
    <div class="columns" style="padding-right: 0; ">
      <ul class="tabs live-stream-homepage" data-tabs id="collapsing-tabs" style="border: none; display: flex; align-items: center; background-color: transparent;">
        <li class="tabs-title is-active" onclick="getFrontPageHearings('todays')"><a href="#todays-hearings" aria-selected="true" data-text="title">Today</a></li>
        <li class="tabs-title" onclick="getFrontPageHearings('tomorrows')"><a href="#tomorrows-hearings" data-text="title">Tomorrow</a></li>
        <li class="tabs-title" onclick="getFrontPageHearings('weeks')"><a href="#week-hearings" data-text="title">This Week</a></li>
      </ul>
      <div class="tabs-content" data-tabs-content="collapsing-tabs" style="border: none; background-color: transparent;">
        <div class="tabs-panel is-active hearings" id="todays-hearings">
          <ul id="fp-todays-hearings" class="row-hearings">
            <!-- Today's hearings here -->
          </ul>
        </div>
        <div class="tabs-panel hearings" id="tomorrows-hearings">
          <ul id="fp-tomorrows-hearings" class="row-hearings">
            <!-- Tomorrow's hearings here -->
          </ul>
        </div>
        <div class="tabs-panel hearings" id="week-hearings">
          <ul id="fp-weeks-hearings" class="row-hearings">
            <!-- Next week's hearings here -->
          </ul>
        </div>
      </div>
    </div>
    <div class="columns hearings-links">
      <a href="https://legistar.council.nyc.gov/Calendar.aspx" target="_blank" rel="noopener noreferrer" style="display:block;"><strong><i class="fa fa-play hearing-links-arrow"></i> View the hearing calendar and video archive here</strong></a>
      <a style="display:block;" href="/testify" target="_blank" rel="noopener"><strong><i class="fa fa-play hearing-links-arrow"></i> Register to testify at one of our upcoming hearings</strong></a>
    </div>
  </div>
</div>

<!--<script>
  /*--------------------------------------------------
    Upcoming Stated Meetings jQuery
  --------------------------------------------------*/
  today = new Date();
  todays_date = {};
  todays_date.year = today.getFullYear();
  todays_date.month = (today.getMonth()+1) < 10 ? "0" + (today.getMonth()+1) : (today.getMonth()+1);
  todays_date.day = today.getDate() < 10 ? "0" + today.getDate() : today.getDate();
  jQuery.ajax({
    type:"GET",
    dataType:"jsonp",
    url:'https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventBodyId+eq+1+and+EventAgendaStatusId+eq+2+and+EventDate+ge+datetime%27'+todays_date.year+"-"+todays_date.month+"-"+todays_date.day+'%27&$orderby=EventDate+asc',
    success:function(meetings){
      if (meetings.length > 0){
        let oneDate = meetings[0]["EventDate"].split("T")[0].split("-");
        let date = new Date(oneDate[0],(parseInt(oneDate[1])-1),oneDate[2]);
        jQuery("#stated").html("Our next <strong>Stated Meeting</strong> will be held on <strong>"+date.toLocaleDateString("en-US",{ weekday: 'long', month: 'long', day: 'numeric' })+"</strong>.")
      } else {
        jQuery("#stated").remove();
      };
    }
  });
</script>-->
<script>
  /*-----------------------------------------------------------------------------------
    Upcoming Council Hearings jQuery - VIRTUAL + IN-PERSON HEARING 1 WEEK IN ADVANCE
  -----------------------------------------------------------------------------------*/
  Date.prototype.stdTimezoneOffset = function() {
    let jan = new Date(this.getFullYear(), 0, 1);
    let jul = new Date(this.getFullYear(), 6, 1);
    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
  };
  Date.prototype.dst = function() {
    // Accounts for daylight savings 1 hour offset
    return this.getTimezoneOffset() < this.stdTimezoneOffset();
  };
  Date.prototype.getWeek = function() {
    let date = new Date(this.valueOf())
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);
    let sunday = date.setDate(date.getDate() - date.getDay());
    date.setHours(23);
    date.setMinutes(59);
    date.setSeconds(59);
    date.setMilliseconds(999);
    let saturday = date.setDate(date.getDate() + 6);
    return [new Date(sunday), new Date(saturday)];
  }
  const getFrontPageHearings = (timeFrame="todays") => {
    const committeeLoader = 
    `<div aria-hidden="true" class="column column-block committee-loader" style="float:none; margin:20px 0; text-align:center; width:100%;">
        <img alt="Loading this week's hearings" aria-hidden="true" src="/wp-content/themes/wp-nycc/assets/images/committee_loader.gif">
    </div>`
    jQuery(`#fp-${timeFrame}-hearings`).empty();
    jQuery(`#fp-${timeFrame}-hearings`).append(committeeLoader);
    let addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
    // let date = new Date('October, 06 2022')
    let date = new Date().dst() ? new Date(new Date().getTime() - 4 * 3600 * 1000) : new Date(new Date().getTime() - 5 * 3600 * 1000);
    let sunday = date.getWeek()[0];
    let saturday = date.getWeek()[1];
    let startDate, endDate, noHearingMessage;
    if (timeFrame === "todays"){
      startDate = date.getFullYear()+"-"+addZero(date.getMonth()+1)+"-"+addZero(date.getDate());
      endDate = startDate;
      noHearingMessage = "TODAY";
    } else if (timeFrame === "tomorrows"){
      date.setDate(date.getDate() + 1);
      startDate = date.getFullYear()+"-"+addZero(date.getMonth()+1)+"-"+addZero(date.getDate());
      endDate = startDate;
      noHearingMessage = "TOMORROW";
    } else if (timeFrame === "weeks") {
      startDate = sunday.getFullYear()+"-"+addZero(sunday.getMonth()+1)+"-"+addZero(sunday.getDate());
      endDate = saturday.getFullYear()+"-"+addZero(saturday.getMonth()+1)+"-"+addZero(saturday.getDate());
      noHearingMessage = "THIS WEEK";
    }
    const bodydExclusions = ["5264"];
    let exclusions = [];
    for (id of bodydExclusions){exclusions.push(`EventBodyId+ne+${id}`)};

    jQuery.ajax({
      type:"GET",
      dataType:"jsonp",
      url:"https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventDate+ge+datetime%27"+startDate+"%27+and+EventDate+le+datetime%27"+endDate+"%27+and+"+exclusions.join("+and+")+"+and+tolower(EventAgendaStatusName)+eq+'final'&$orderby=EventTime+asc",
      success:function(hearings){
        function dateTimeConverter(dateString, timeString){
          let fullDate = dateString.split("T")[0].split("-");
          let year = parseInt(fullDate[0]);
          let month = parseInt(fullDate[1])-1;
          let date = parseInt(fullDate[2]);
          let hr = parseInt(timeString.split(" ")[0].split(":")[0]);
          let min = parseInt(timeString.split(" ")[0].split(":")[1]);
          let ampm = timeString.split(" ")[1];
          ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr : hr = (hr+12);
          return new Date(year, month, date, hr, min, 00);
        };
        let sortedHearings = hearings.sort(function(a,b){
          return dateTimeConverter(a.EventDate, a.EventTime).getTime() - dateTimeConverter(b.EventDate, b.EventTime).getTime();
        });

        jQuery(`#fp-${timeFrame}-hearings .committee-loader`).remove();
        if (hearings.length < 5){
          jQuery(`#fp-${timeFrame}-hearings`).addClass("horizontal-grid");
        }
        if (hearings.length === 0){
          jQuery(`#fp-${timeFrame}-hearings`).append(`<li class='column column-block no-hearings' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO SCHEDULED HEARINGS ${noHearingMessage}</em></li>`);
          jQuery('.row-hearings.horizontal-grid').css('grid-auto-columns','auto');
        } else {
          sortedHearings.forEach(function(hearing){
            let deferred = hearing.EventAgendaStatusName.toLowerCase() === "deferred";
            let hearingName = "<strong>"+hearing.EventBodyName+"</strong><br>";
            let meetingDate = hearing.EventDate.split("T")[0];
            let meetingDateFormat = new Date(meetingDate.split("-")[0], parseInt(meetingDate.split("-")[1])-1, meetingDate.split("-")[2]);
            let livestreamLocation = hearing.EventLocation.match(/\(([^)]+)\)/);
            if(livestreamLocation) {
              livestreamLocation = livestreamLocation[1];
            } else {
              livestreamLocation = hearing.EventLocation;
            };
            meetingDate = meetingDateFormat.toDateString().split(" ");
            let monthOnly = meetingDate[1];
            let dayNum = meetingDate[2];
            let suffix;

            switch(dayNum.slice(-1)){
              case "1":
                suffix = "ST";
                break;
              case "2":
                suffix = "ND";
                break;
              case "3":
                suffix = "RD";
                break;
              default:
                suffix = "TH";
            };
            midDay = hearing.EventTime.split(" ")[1];
            meetingHour = parseInt(hearing.EventTime.split(" ")[0].split(":")[0]);
            meetingMinute = parseInt(hearing.EventTime.split(" ")[0].split(":")[1]);
            hearing.EventAgendaFile !== null ? agendaLink = hearing.EventAgendaFile : agendaLink = "#";
            midDay === "PM" && meetingHour !== 12 ? meetingHour += 12 : meetingHour;
            if (hearing.EventComment !== null){
              if(hearing.EventComment.toLowerCase().includes("jointly") && !hearing.EventLocation.toLowerCase().includes("-")){
                  hearingName += "<small><em>("+hearing.EventComment+")</em></small><br>"
              };
            };
            jQuery(`#fp-${timeFrame}-hearings`).append(`
              <li class="columns" style="padding: .5em; display:flex; ${deferred ? "text-decoration: line-through;" : ""}">
                <div class="card hearing-card" style="border: none;">
                  <div class="card-divider hearing-date" style="background-color:transparent;">
                    ${deferred ? "<p style='position: absolute;right: 16px;top: 16px;font-size: 18pt;font-family: Gotham-Black;color: #FFF;text-decoration: none !important;'>DEFERRED</p>" : ""}
                    <div class="hearing-date-tab">
                      <span>${monthOnly.toUpperCase()}</span><br/>
                      <span>${parseInt(dayNum,10).toString()}<sup><small>${suffix}</small></sup></span><br/>
                      <span class="hearing-time" style="text-align: right;">${hearing.EventTime}</span>
                    </div>
                  </div>
                  <p style="margin-bottom: 0;" class="card-section hearing-body"><a href='${agendaLink}' target='_blank'>${hearingName}</a></h5>
                  <p class="card-section hearing-stream-link" style="line-height:1"><a href="https://council.nyc.gov/livestream/#${livestreamLocation.toLowerCase().replace(/[^\w\s\-]/gi, '').split(" ").join("-")}">${livestreamLocation}</a></p>
                </div>
              </li>
            `);
          });
        };
      }
    });
  };
  jQuery(document).ready(() => {
    let hash = window.location.hash;
    if (hash === "#todays-hearings" || hash === ""){
      getFrontPageHearings("todays");
    } else if (hash === "#tomorrows-hearings"){
      getFrontPageHearings("tomorrows");
    } else if (hash === "#week-hearings"){
      getFrontPageHearings("weeks");
    };
  });
</script>