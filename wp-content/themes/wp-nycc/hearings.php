<div id="hearings-section">
  <div class="row">
    <div class="columns">
      <h2 style="position: relative;">
        UPCOMING LEGISLATIVE HEARINGS
        <span style="position: absolute; right: 0; top: 0;"><a href="/testify/" class="button large">SIGN UP TO TESTIFY</a></span>
        <hr style="border-bottom: 1px solid #58595B; margin: 30px 0;"/>
      </h2>
    </div>
  </div>
  <div class="row">
    <select id="hearing-type-filter" class="hearing-filter column medium-6 large-3">
      <option value="" hidden>--- Filter by Type ---</option>
      <option value="eq+1">Stated Meeting</option>
      <option value="ne+1">Committee Hearing</option>
    </select>
    <select id="committee-filter" class="hearing-filter column medium-6 large-3"><option value="" hidden>--- Filter by Committee ---</option></select>
    <select id="location-filter" class="hearing-filter column medium-6 large-3">
      <option value="" hidden>--- Filter by Location ---</option>
      <option value="Council Chambers - City Hall">Council Chambers - City Hall</option>
      <option value="Committee Room - City Hall">Committee Room - City Hall</option>
      <option value="250 Broadway - Committee Room, 14th Floor">250 Broadway - Committee Room, 14th Floor</option>
      <option value="250 Broadway - Committee Room, 16th Floor">250 Broadway - Committee Room, 16th Floor</option>
    </select>
    <!-- Need to add input for a date range -->
  </div>
  <ol id="hearings" class="hearings-lists">
  </ol>
  <div class="row">
    <div class="columns hearings-links">
      <a href="https://legistar.council.nyc.gov/Calendar.aspx" target="_blank" rel="noopener noreferrer" style="display:block;"><strong><i class="fa fa-play hearing-links-arrow"></i> View the hearing calendar and video archive here</strong></a>
      <a style="display:block;" href="/testify" target="_blank" rel="noopener"><strong><i class="fa fa-play hearing-links-arrow"></i> Register to testify at one of our upcoming hearings</strong></a>
    </div>
  </div>
</div>
<script>
  /*-----------------------------------------------------------------------------------
    Populate hearings filter
  -----------------------------------------------------------------------------------*/
  /*  
    BodyTypeId === 1      "Primary Legislative Body" aka "City Council" aka "Stated Meetings"
    BodyTypeId === 2      "Committees
    BodyTypeId === 5003   "Subcommittees"
    BodyId === 5127       "By the Committee on Rules, Privileges & Elections" BAD COMMITTEE
    BodyActiveFlag === 1  "Denotes an active body
  */
  jQuery("document").ready(() => {
    jQuery.ajax({
      type:"GET",
        dataType:"jsonp",
        url:`https://webapi.legistar.com/v1/nyc/bodies?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$orderby=BodyName+asc&$filter=BodyId+ne+5127+and+BodyActiveFlag+eq+1+and+(BodyTypeId+eq+1+or+BodyTypeId+eq+2+or+BodyTypeId+eq+5003)`,
        success:function(committees){
          let options = "";
          for (let committee of committees){ options += `<option value="${committee.BodyId}">${committee.BodyName}</option>` };
          jQuery("#committee-filter").append(options);
        };
    });
  });
</script>
<script>
  /*-----------------------------------------------------------------------------------
    Retrieve and render this current week's hearings
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
  const getFrontPageHearings = () => {
    const hearingListElement = jQuery('#hearings.hearings-lists');
    hearingListElement.empty();
    const hearingLoader = 
    `<div aria-hidden="true" class="column column-block committee-loader" style="float:none; margin:20px 0; text-align:center; width:100%;">
        <img alt="Loading this week's hearings" aria-hidden="true" src="/wp-content/themes/wp-nycc/assets/images/committee_loader.gif">
    </div>`;
    hearingListElement.append(hearingLoader);
    let addZero = function(n) {return (n < 10) ? ("0" + n) : n;}
    // let date = new Date('October, 06 2022')
    let date = new Date().dst() ? new Date(new Date().getTime() - 4 * 3600 * 1000) : new Date(new Date().getTime() - 5 * 3600 * 1000);
    let sunday = date.getWeek()[0];
    let saturday = date.getWeek()[1];
    let startDate = sunday.getFullYear()+"-"+addZero(sunday.getMonth()+1)+"-"+addZero(sunday.getDate());
    let endDate = saturday.getFullYear()+"-"+addZero(saturday.getMonth()+1)+"-"+addZero(saturday.getDate());

    jQuery.ajax({
      type:"GET",
      dataType:"jsonp",
      url:`https://webapi.legistar.com/v1/nyc/events?token=Uvxb0j9syjm3aI8h46DhQvnX5skN4aSUL0x_Ee3ty9M.ew0KICAiVmVyc2lvbiI6IDEsDQogICJOYW1lIjogIk5ZQyByZWFkIHRva2VuIDIwMTcxMDI2IiwNCiAgIkRhdGUiOiAiMjAxNy0xMC0yNlQxNjoyNjo1Mi42ODM0MDYtMDU6MDAiLA0KICAiV3JpdGUiOiBmYWxzZQ0KfQ&$filter=EventDate+ge+datetime%27${startDate}%27+and+EventDate+le+datetime%27${endDate}%27+and+tolower(EventAgendaStatusName)+eq+'final'&$orderby=EventTime+asc`,
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

        hearingListElement.empty();
        if (hearings.length === 0){
          hearingListElement.append(`<li class='column column-block no-hearings' style='float:none;margin:20px 0;text-align:center;width:100%;'><em>NO SCHEDULED HEARINGS THIS WEEK</em></li>`);
        } else {
          // Creating the "skeleton" data structure for organizing hearings into time and dates
          let orgDates = {};
          let times, dates = Array.from(new Set(hearings.map(hearing => hearing.EventDate)));
          for (let date of dates) { // date = 2023-03-27T00:00:00
              let hearingsOnDate = hearings.filter(hearing => hearing.EventDate === date);
              times = Array.from(new Set(hearingsOnDate.map(hearing => hearing.EventTime)));
              let orgTimes = {};
              for (let time of times){
                  let filteredHearings = hearingsOnDate.filter(hearing => hearing.EventTime === time);
                  orgTimes[time] = filteredHearings;
              };
              orgDates[date] = orgTimes;
          }
          // Sort weekly hearings into the pre-built data structure
          let olHearings = "";
          for (let date of dates){
            let htmlIndividualHearings = "", htmlHearingTimes = "", htmlAllHearings = "", longDate = new Date(date).toLocaleDateString("default",{"day":"numeric","month":"long"}); // March 29
            switch(longDate.slice(-1)){
              case "1":
                suffix = "st";
                break;
              case "2":
                suffix = "nd";
                break;
              case "3":
                suffix = "rd";
                break;
              default:
                suffix = "th";
            };
            longDate = longDate + suffix; // March 29th
            for (let time of times){
              let hearings = orgDates[date][time]
              if (hearings){
                for (let hearing of hearings){
                  let jointly = ""
                  if (hearing.EventComment !== null){
                    if(hearing.EventComment.toLowerCase().includes("jointly") && !hearing.EventLocation.toLowerCase().includes("-")){
                      jointly += `<br/><small><em>(${hearing.EventComment})</em></small>`
                    };
                  };
                  htmlIndividualHearings += `
                    <li>
                      <h5>${hearing.EventBodyId === 1 ? "Stated Meeting" : hearing.EventBodyName}${jointly}<br/><small>${hearing.EventLocation}</small></h5>
                    </li>
                  `;
                }
                htmlHearingTimes = `
                  <li>
                    <h4>${time}</h4>
                      <ol id="${longDate.toLowerCase().replace(" ","-")}-hearings-at-${time}" class="hearings-lists">
                        ${htmlIndividualHearings}
                      </ol>
                    <hr/>
                  </li>
                `;
              }
              htmlAllHearings = `<ol id="${longDate.toLowerCase().replace(" ","-")}-hearings" class="column medium-9 hearings-lists">${htmlHearingTimes}</ol>`;
            }
            hearingListElement.append(`
              <li class="row" id="${longDate.toLowerCase().replace(" ","-")}-row">
                <div id="${longDate.toLowerCase().replace(" ","-")}-date" class="column medium-3">
                  <h3>${longDate}</h3>
                </div>
                ${htmlAllHearings}
              </li>
            `);
          };
        };
      }
    });
  };
  jQuery("document").ready(() => {
    getFrontPageHearings();
    // Add event listener for filter dropdowns
  });
</script>