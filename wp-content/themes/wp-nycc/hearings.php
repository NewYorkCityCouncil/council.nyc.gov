<div class="row" style="margin-bottom: 6rem">
  <div class="columns">
    <ul class="tabs live-stream-homepage" data-tabs id="collapsing-tabs" style="border: none; display: flex; align-items: center; justify-content: center;">
      <li class="tabs-title is-active"><a href="#panel1c" aria-selected="true" data-text="title">Today's Hearings</a></li>
      <li class="tabs-title"><a href="#panel2c" data-text="title">Tomorrow's Hearings</a></li>
      <li class="tabs-title"><a href="#panel3c" data-text="title">This Week's Hearings</a></li>
    </ul>
    <div class="tabs-content" data-tabs-content="collapsing-tabs" style="border: none;">
      <div class="tabs-panel is-active" id="panel1c">
        <div id="fp-hearings" class="row small-up-1 medium-up-2 large-up-4">
          <div id="committee-loader" aria-hidden="true" class="column column-block" style="float:none; margin:20px 0; text-align:center; width:100%;">
            <img alt="Loading this week's hearings" aria-hidden="true" src="/wp-content/themes/wp-nycc/assets/images/committee_loader.gif"></div>
        </div>
      </div>
      <div class="tabs-panel" id="panel2c">
        <div class="row small-up-1 medium-up-2 large-up-4">
          <div class="columns" style="padding: .5em;">
            <div class="card" style="background-color: #E6E6E6; border: none;">
              <div class="card-divider" style="margin-bottom: 2em;">
                <div class="row">
                  <div class="columns small-6">
                    <h4 style="margin-bottom: 0; line-height: 0.8em">JUN</h4>
                    <h4 style="margin-bottom: 0; line-height: 0.8em">30</h4>
                  </div>
                  <div class="columns small-6" style="text-align: right;">10:00 AM</div>
                </div>
              </div>
              <div class="card-section">
                <h5 style="margin-bottom: 0px">This is a row of cards.</h5>
                <p>This row of cards is embedded in an X-Y Block Grid.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="tabs-panel" id="panel3c">
        <div class="row small-up-1 medium-up-2 large-up-4">
          <div class="columns" style="padding: .5em;">
            <div class="card" style="background-color: #E6E6E6; border: none;">
              <div class="card-divider" style="margin-bottom: 2em;">
                <div class="row">
                  <div class="columns small-6">
                    <h4 style="margin-bottom: 0; line-height: 0.8em">JUN</h4>
                    <h4 style="margin-bottom: 0; line-height: 0.8em">30</h4>
                  </div>
                  <div class="columns small-6" style="text-align: right;">10:00 AM</div>
                </div>
              </div>
              <div class="card-section">
                <h5 style="margin-bottom: 0">This is a row of cards.</h5>
                <p>This row of cards is embedded in an X-Y Block Grid.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="columns">
    <a href="https://legistar.council.nyc.gov/Calendar.aspx" target="_blank" rel="noopener noreferrer" style="display:block;"><strong>View the hearing calendar and video archive here.</strong></a>
    <a style="display:block;" href="/testify" target="_blank" rel="noopener"><strong>Register to testify at one of our upcoming hearings</strong></a>
  </div>
</div>


<script>
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
</script>
<script>
  /*-----------------------------------------------------------------------------------
    Upcoming Council Hearings jQuery - VIRTUAL + IN-PERSON HEARING 1 WEEK IN ADVANCE
  -----------------------------------------------------------------------------------*/
  Date.prototype.stdTimezoneOffset = function() {
    let jan = new Date(this.getFullYear(), 0, 1);
    let jul = new Date(this.getFullYear(), 6, 1);
    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
  }
  Date.prototype.dst = function() {
    // Accounts for daylight savings 1 hour offset
    return this.getTimezoneOffset() < this.stdTimezoneOffset();
  }
  Date.prototype.getWeek = function() {
    let date = new Date(this.valueOf())
    date.setHours(0)
    date.setMinutes(0)
    date.setSeconds(0)
    date.setMilliseconds(0)
    let sunday = date.setDate(date.getDate() - date.getDay());
    date.setHours(23)
    date.setMinutes(59)
    date.setSeconds(59)
    date.setMilliseconds(999)
    let saturday = date.setDate(date.getDate() + 6);
    return [new Date(sunday), new Date(saturday)];
  }
  let addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
  let date = new Date().dst() ? new Date(new Date().getTime() - 4 * 3600 * 1000) : new Date(new Date().getTime() - 5 * 3600 * 1000)
  let sunday = date.getWeek()[0]
  let saturday = date.getWeek()[1]

  let startDate = sunday.getFullYear()+"-"+addZero(sunday.getMonth()+1)+"-"+addZero(sunday.getDate());
  let endDate = saturday.getFullYear()+"-"+addZero(saturday.getMonth()+1)+"-"+addZero(saturday.getDate());
  console.log(startDate)
  console.log(endDate)
  const bodydExclusions = ["5264"]
  let exclusions = []
  for (id of bodydExclusions){exclusions.push(`EventBodyId+ne+${id}`)}

  jQuery.ajax({
    type:"GET",
    dataType:"jsonp",
    url:"https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventDate+ge+datetime%27"+startDate+"%27+and+EventDate+lt+datetime%27"+endDate+"%27+and+"+exclusions.join("+and+")+"+and+tolower(EventAgendaStatusName)+eq+'final'&$orderby=EventTime+asc",
    success:function(hearings){
      function dateTimeConverter(dateString, timeString){
        let fullDate = dateString.split("T")[0].split("-")
        let year = parseInt(fullDate[0])
        let month = parseInt(fullDate[1])
        let date = parseInt(fullDate[2])
        let hr = parseInt(timeString.split(" ")[0].split(":")[0]);
        let min = parseInt(timeString.split(" ")[0].split(":")[1]);
        let ampm = timeString.split(" ")[1];
        ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr : hr = (hr+12) ;
        return new Date(year, month, date, hr, min, 00)
      };
      let sortedHearings = hearings.sort(function(a,b){
        return dateTimeConverter(a.EventDate, a.EventTime).getTime() - dateTimeConverter(b.EventDate, b.EventTime).getTime();
      });

      jQuery("#committee-loader").remove();
      if (hearings.length === 0){
        jQuery("#fp-hearings").append("<li class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO SCHEDULED HEARINGS THIS WEEK</em></li>");
      } else {
        sortedHearings.forEach(function(hearing){
          let hearingName = "<strong>"+hearing.EventBodyName+"</strong><br>"
          let meetingDate = hearing.EventDate.split("T")[0];
          let meetingDateFormat = new Date(meetingDate.split("-")[0], parseInt(meetingDate.split("-")[1])-1, meetingDate.split("-")[2])
          let livestreamLocation = hearing.EventLocation.match(/\(([^)]+)\)/)
          if(livestreamLocation) {
            livestreamLocation = livestreamLocation[1];
          } else {
            livestreamLocation = hearing.EventLocation;
          }
          meetingDate = meetingDateFormat.toDateString().split(" ")
          monthOnly = meetingDate[1]
          dayNum = meetingDate[2]
          meetingDate.pop()
          meetingDate[0] = meetingDate[0] + ","
          meetingDate = meetingDate.join(" ")
          midDay = hearing.EventTime.split(" ")[1];
          meetingHour = parseInt(hearing.EventTime.split(" ")[0].split(":")[0]);
          meetingMinute = parseInt(hearing.EventTime.split(" ")[0].split(":")[1]);
          hearing.EventAgendaFile !== null ? agendaLink = hearing.EventAgendaFile : agendaLink = "#";
          midDay === "PM" && meetingHour !== 12 ? meetingHour += 12 : meetingHour;
          if (hearing.EventComment !== null){
            if(hearing.EventComment.toLowerCase().includes("jointly") && !hearing.EventLocation.toLowerCase().includes("-")){
                hearingName += "<small><em>("+hearing.EventComment+")</em></small><br>"
            }
          }
          if(hearing.EventAgendaStatusName.toLowerCase() === "deferred"){
            jQuery("#fp-hearings").append(`
              <div class="columns" style="padding: .5em;" aria-label='deferred hearing'>
                <div class="card" style="background-color: #E6E6E6; border: none;">
                  <div class="card-divider" style="margin-bottom: 2em;">
                    <div class="row">
                      <div class="columns small-6">
                        <h4 style="margin-bottom: 0; line-height: 0.8em">`+monthOnly+`</h4>
                        <h4 style="margin-bottom: 0; line-height: 0.8em">`+dayNum+`</h4>
                      </div>
                      <div class="columns small-6" style="text-align: right;">`+hearing.EventTime+`</div>
                    </div>
                  </div>
                  <div class="card-section">
                    <h5 style="margin-bottom: 0; font-size: 1rem; line-height: 1.5em;"><a href='`+agendaLink+`' target='_blank'>`+hearingName+`</a></h5>
                    <p>`+livestreamLocation+`</p>
                  </div>
                </div>
              </div>
            `);
          } else {
            jQuery("#fp-hearings").append(`
              <div class="columns" style="padding: .5em;" aria-label='deferred hearing'>
                <div class="card" style="background-color: #E6E6E6; border: none;">
                  <div class="card-divider" style="margin-bottom: 2em;">
                    <div class="row">
                      <div class="columns small-6">
                        <h4 style="margin-bottom: 0; line-height: 0.8em">`+monthOnly+`</h4>
                        <h4 style="margin-bottom: 0; line-height: 0.8em">`+dayNum+`</h4>
                      </div>
                      <div class="columns small-6" style="text-align: right;">`+hearing.EventTime+`</div>
                    </div>
                  </div>
                  <div class="card-section">
                    <h5 style="margin-bottom: 0; font-size: 1rem; line-height: 1.5em;"><a href='`+agendaLink+`' target='_blank'>`+hearingName+`</a></h5>
                    <p><a href='https://council.nyc.gov/livestream/#"+livestreamLocation.toLowerCase().replace(/[^\w\s\-]/gi, '').split(" ").join("-")+"'>`+livestreamLocation+`</a></p>
                  </div>
                </div>
              </div>
            `);
          };
        });
        if (jQuery("#fp-hearings").children().length === 0){
          jQuery("#fp-hearings").append("<li class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO SCHEDULED HEARINGS THIS WEEK</em></li>");
        };
      };
    }
  });
</script>

<!-- <li class='columns column-block' aria-label='deferred hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'>"+hearingName+"</a><i class='fa fa-calendar' aria-hidden='true'></i> <small><s>"+meetingDate+"</s> Deferred</small><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small><s>"+hearing.EventTime+"</s> Deferred</small><br><i class='fas fa-podcast'></i> <small><s>"+livestreamLocation+"</s></small></li>"); -->

<!-- <li class='columns column-block' aria-label='scheduled hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'>"+hearingName+"</a><i class='fa fa-calendar' aria-hidden='true'></i> <small>"+meetingDate+"</small><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small>"+hearing.EventTime+"</small><br><i class='fas fa-podcast'></i> <small><a href='https://council.nyc.gov/livestream/#"+livestreamLocation.toLowerCase().replace(/[^\w\s\-]/gi, '').split(" ").join("-")+"'>"+livestreamLocation+"</a></small></li> -->

<script>
  /*-----------------------------------------------------------------------------------
    Upcoming Council Hearings jQuery - IN-PERSON HEARINGS DAY OFF
  -----------------------------------------------------------------------------------*/
  /*Date.prototype.stdTimezoneOffset = function() {
    let jan = new Date(this.getFullYear(), 0, 1);
    let jul = new Date(this.getFullYear(), 6, 1);
    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
  }
  Date.prototype.dst = function() {
    return this.getTimezoneOffset() < this.stdTimezoneOffset();
  }
  let addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
  let date;
  date = new Date().dst() ? new Date(new Date().getTime() - 4 * 3600 * 1000) : new Date(new Date().getTime() - 5 * 3600 * 1000)
  let month31 = [1,3,5,7,8,10,12], month30 = [4,6,9,11], startDate, endDate, startYear = date.getFullYear(), startMonth = date.getMonth()+1, startDay = date.getDate(), nowHour = date.getUTCHours(), nowMinute = date.getUTCMinutes(), midDay, meetingHour, meetingMinute, endYear, endMonth, endDay, agendaLink;
  if(startMonth === 12 && startDay === 31){ // if start day is NYE. Unlikely.
    endYear = startYear+1;
    endMonth = 1;
    endDay = 1;
  } else if (startYear%4 !== 0 && startMonth === 2 && startDay === 28){ //if last day of Feb in normal year
    endYear = startYear;
    endMonth = 3;
    endDay = 1;
  } else if (startYear%4 === 0 && startMonth === 2 && startDay === 29){ //if last day of Feb in leap year
    endYear = startYear;
    endMonth = 3;
    endDay = 1;
  } else if ((month31.indexOf(startMonth) !== -1) && startDay === 31){ //if start day is 31st day of month
    endYear = startYear;
    endMonth = startMonth+1;
    endDay = 1;
  } else if ((month30.indexOf(startMonth) !== -1) && startDay === 30){ //if start day is 30th day of month
    endYear = startYear;
    endMonth = startMonth+1;
    endDay = 1;
  } else { //any other day
    endYear = startYear;
    endMonth = startMonth;
    endDay = startDay+1;
  };

  startDate = startYear+"-"+addZero(startMonth)+"-"+addZero(startDay);
  endDate = endYear+"-"+addZero(endMonth)+"-"+addZero(endDay);
  jQuery.ajax({
    type:"GET",
    dataType:"jsonp",
    url:"https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventDate+ge+datetime%27"+startDate+"%27+and+EventDate+lt+datetime%27"+endDate+"%27+and+tolower(EventAgendaStatusName)+ne+'draft'&$orderby=EventTime+asc",
    success:function(hearings){
      function timeConverter(timeString){
        let hr = parseInt(timeString.split(" ")[0].split(":")[0]);
        let min = parseInt(timeString.split(" ")[0].split(":")[1]);
        let ampm = timeString.split(" ")[1];
        ampm.toLowerCase() === "am" || (ampm.toLowerCase() === "pm" && hr === 12) ? hr = hr : hr = (hr+12);
        return hr+min;
      };
      let sortedHearings = hearings.sort(function(a,b){
        return timeConverter(a.EventTime) - timeConverter(b.EventTime);
      });

      jQuery("#committee-loader").remove();
      if (hearings.length === 0){
        jQuery("#front-page-hearings").append("<div class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></div>");
      } else {
        sortedHearings.forEach(function(hearing){
          let hearingName = "<strong>"+hearing.EventBodyName+"</strong><br>"
          let meetingDate = hearing.EventDate.split("T")[0];
          let meetingDateFormat = new Date(meetingDate.split("-")[0], parseInt(meetingDate.split("-")[1])-1, meetingDate.split("-")[2])
          let livestreamLocation = hearing.EventLocation.match(/\(([^)]+)\)/)[1];
          meetingDate = meetingDateFormat.toDateString().split(" ")
          meetingDate.pop()
          meetingDate[0] = meetingDate[0] + ","
          meetingDate = meetingDate.join(" ")
          midDay = hearing.EventTime.split(" ")[1];
          meetingHour = parseInt(hearing.EventTime.split(" ")[0].split(":")[0]);
          meetingMinute = parseInt(hearing.EventTime.split(" ")[0].split(":")[1]);
          hearing.EventAgendaFile !== null ? agendaLink = hearing.EventAgendaFile : agendaLink = "#";
          midDay === "PM" && meetingHour !== 12 ? meetingHour += 12 : meetingHour;
          if (hearing.EventComment !== null){
            if(hearing.EventComment.toLowerCase().includes("jointly") && !hearing.EventLocation.toLowerCase().includes("-")){
                hearingName += "<small><em>("+hearing.EventComment+")</em></small><br>"
            } else if (hearing.EventComment.toLowerCase().includes("jointly") && hearing.EventLocation.toLowerCase().includes("-")){
                return
            }
          }
          if(hearing.EventAgendaStatusName.toLowerCase() === "deferred"){
            jQuery("#front-page-hearings").append("<li class='columns column-block' aria-label='deferred hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small><s>"+hearing.EventTime+"</s> Deferred</small><br><i class='fas fa-map-marker-alt'></i> <small>"+hearing.EventLocation+"</small></li>");
          } else {
            jQuery("#front-page-hearings").append("<li class='columns column-block' aria-label='scheduled hearing' style='margin-bottom:10px;'><a href='"+agendaLink+"' target='_blank'><strong>"+hearing.EventBodyName+"</strong></a><br><i class='fa fa-clock-o' aria-hidden='true'></i> <small>"+hearing.EventTime+"</small><br><i class='fas fa-map-marker-alt'></i> <small>"+hearing.EventLocation+"</small></li>");
          };
        });
        if (jQuery("#front-page-hearings").children().length === 0){
          jQuery("#front-page-hearings").append("<li class='column column-block' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO UPCOMING HEARINGS TODAY</em></li>");
        };
      };
    }
  });*/
</script>