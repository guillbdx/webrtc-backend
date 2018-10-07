let cursor = 0;
let photos = [];
let calendar = [];
const pathBrowseAllPhotos = document.getElementById('pathBrowseAllPhotos').value;

//----------------------------------------------------
// DISPLAYABLE DATES FUNCTIONS
//----------------------------------------------------

function pad(n) {
    n = n + '';
    return n.length >= 2 ? n : new Array(2 - n.length + 1).join('0') + n;
}

function getDisplayableDate(photo) {
    let date = new Date(photo.timestamp*1000);
    let dateString = pad(String(date.getDate())) + '/'
        + pad(String(date.getMonth() + 1)) + '/'
        + String(date.getFullYear()) + ' '
        + pad(String(date.getHours())) + ':'
        + pad(String(date.getMinutes())) + ':'
        + pad(String(date.getSeconds()))
    ;

    return dateString;
}

function getDisplayableMonth(month) {

    month = month.split('-');
    let year = month[1];
    month = parseInt(month[0]);
    let monthStrings = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    let monthString = monthStrings[month];
    return monthString + ' ' + year;
}

function getDisplayableSecond(photo) {
    let date = new Date(photo.timestamp*1000);
    let dateString = pad(String(date.getMinutes())) + ':'
        + pad(String(date.getSeconds()))
    ;

    return dateString;
}

//----------------------------------------------------
// CONSTRUCT CALENDAR FUNCTIONS
//----------------------------------------------------

function createCalendar() {
    for (let i = 0; i < photos.length; i++) {
        let photo = photos[i];
        let date = new Date(photo.timestamp*1000);

        // Month
        let monthYear = date.getMonth()+'-'+date.getFullYear();
        if (!(monthYear in calendar)) {
            calendar[monthYear] = [];
        }

        // Day
        let day = date.getDate();
        if (!(day in calendar[monthYear])) {
            calendar[monthYear][day] = [];
        }

        // Hour
        let hour = date.getHours();
        if (!(hour in calendar[monthYear][day])) {
            calendar[monthYear][day][hour] = [];
        }

        // Second
        calendar[monthYear][day][hour].push(photo);
    }
}

//----------------------------------------------------
// FIRST DISPLAY CALENDAR FUNCTIONS
//----------------------------------------------------

function displayMonthLink(month) {
    let link = '<a href="#" ' +
        'id="browserLink-'+month+'" ' +
        'style="display: block;" ' +
        'class="monthLink" ' +
        'data-month="'+month+'">';
    link += getDisplayableMonth(month);
    link += '</a>';
    jQuery('#months').append(link);
}

function displayDayLink(month, day) {
    let link = '<a href="#" ' +
        'id="browserLink-'+month+'-'+day+'" ' +
        'style="display: block;" ' +
        'class="dayLink month-' + month + '" ' +
        'data-month="'+month+'" ' +
        'data-day="'+day+'">';
    link += pad(String(day));
    link += '</a>';
    jQuery('#days').append(link);
}

function displayHourLink(month, day, hour) {
    let link = '<a href="#" ' +
        'id="browserLink-'+month+'-'+day+'-'+hour+'" ' +
        'style="display: block;" ' +
        'class="hourLink month-' + month + ' day-' + day + '" ' +
        'data-month="'+month+'" ' +
        'data-day="'+day+'" ' +
        'data-hour="'+hour+'">';
    link += pad(String(hour));
    link += '</a>';
    jQuery('#hours').append(link);
}

function displaySecondLink(month, day, hour, photo) {
    let link = '<a href="#" ' +
        'id="browserLink-'+month+'-'+day+'-'+hour+'-'+photo.id+'" ' +
        'style="display: block;" ' +
        'data-photo="'+photo.id+'" ' +
        'class="secondLink month-' + month + ' day-' + day + ' hour-' + hour + '">';
    link += getDisplayableSecond(photo);

    if (photo.mismatchedPhoto != null) {
        link += ' <span style="color: red;">[' + photo.mismatch / 100 + ' %]</span>';
    }

    link += '</a>';
    jQuery('#seconds').append(link);
}

function displayCalendar() {
    for (let month in calendar) {
        displayMonthLink(month);
        for (let day in calendar[month]) {
            displayDayLink(month, day);
            for (let hour in calendar[month][day]) {
                displayHourLink(month, day, hour);
                for (let second in calendar[month][day][hour]) {
                    displaySecondLink(month, day, hour, calendar[month][day][hour][second]);
                }
            }
        }
    }
}

//----------------------------------------------------
// FILTER DISPLAY CALENDAR FUNCTIONS
//----------------------------------------------------

function filterDaysForGivenMonth(month) {
    jQuery('.dayLink').hide();
    jQuery('.dayLink.month-'+month).show();
    jQuery('.hourLink').hide();
    jQuery('.secondLink').hide();
}

function filterHoursForGivenDay(month, day) {
    jQuery('.hourLink').hide();
    jQuery('.hourLink.month-'+month+'.day-'+day).show();
    jQuery('.secondLink').hide();
}

function filterSecondsForGivenHour(month, day, hour) {
    jQuery('.secondLink').hide();
    jQuery('.secondLink.month-'+month+'.day-'+day+'.hour-'+hour).show();
}

//----------------------------------------------------
// SELECT THE RIGHT PHOTO IN CALENDAR LIST
//----------------------------------------------------

function selectPhotoInCalendar(photo) {
    let date = new Date(photo.timestamp*1000);
    let monthYear = date.getMonth()+'-'+date.getFullYear();
    let day = date.getDate();
    let hour = date.getHours();

    jQuery('.monthLink').css('font-weight', 'normal');
    jQuery('#browserLink-'+monthYear).css('font-weight', 'bold');
    filterDaysForGivenMonth(monthYear);

    jQuery('.dayLink').css('font-weight', 'normal');
    jQuery('#browserLink-'+monthYear+'-'+day).css('font-weight', 'bold');
    filterHoursForGivenDay(monthYear, day);

    jQuery('.hourLink').css('font-weight', 'normal');
    jQuery('#browserLink-'+monthYear+'-'+day+'-'+hour).css('font-weight', 'bold');
    filterSecondsForGivenHour(monthYear, day, hour);

    jQuery('.secondLink').css('font-weight', 'normal');
    jQuery('#browserLink-'+monthYear+'-'+day+'-'+hour+'-'+photo.id).css('font-weight', 'bold');
}

//----------------------------------------------------
// PHOTOS FUNCTIONS
//----------------------------------------------------

function findPhoto(idPhoto) {
    for (let i = 0; i < photos.length; i++) {
        if (photos[i].id === idPhoto) {
            cursor = i;
            return photos[i];
        }
    }
}

function changeDownloadLink(photo) {
    let newUrl = '/photo/show/' + photo.id + '/1';
    jQuery('#download').attr('href', newUrl);
}

function loadPhoto(photo) {
    jQuery('#browserImg').attr('src', '/photo/show/' + photo.id);
    jQuery('#photoDate').text(getDisplayableDate(photo));
    getDisplayableDate(photo);
    changeDownloadLink(photo);
}

function displayButtons() {
    jQuery('#leftButton').show();
    jQuery('#rightButton').show();
    if (cursor >= photos.length - 1) {
        jQuery('#rightButton').hide();
    }
    if (cursor <= 0) {
        jQuery('#leftButton').hide();
    }
}

function loadPreviousPhoto() {
    cursor -= 1;
    if (cursor < 0) {
        return;
    }
    var newPhoto = photos[cursor];
    loadPhoto(newPhoto);
    selectPhotoInCalendar(newPhoto);
}

function loadNextPhoto() {
    cursor += 1;
    if (cursor >= photos.length) {
        return;
    }
    var newPhoto = photos[cursor];
    loadPhoto(newPhoto);
    selectPhotoInCalendar(newPhoto);
}

//----------------------------------------------------
// EVENTS
//----------------------------------------------------

jQuery('#browserImg').on('load', function() {
    jQuery('#loading').hide();
    jQuery('#photoDate').show();
    displayButtons();
});

jQuery('#leftButton').click(function() {
    jQuery('#loading').show();
    jQuery('#photoDate').hide();
    loadPreviousPhoto();
    return false;
});

jQuery('#rightButton').click(function() {
    jQuery('#loading').show();
    jQuery('#photoDate').hide();
    loadNextPhoto();
    return false;
});

function listenEvents() {

    jQuery('.monthLink').click(function() {

        jQuery('.monthLink').css('font-weight', 'normal');
        jQuery(this).css('font-weight', 'bold');

        let month = jQuery(this).attr('data-month');
        filterDaysForGivenMonth(month);
        let firstDayLink = jQuery('.dayLink:visible').first();
        firstDayLink.click();
        return false;
    });

    jQuery('.dayLink').click(function() {

        jQuery('.dayLink').css('font-weight', 'normal');
        jQuery(this).css('font-weight', 'bold');

        let month = jQuery(this).attr('data-month');
        let day = jQuery(this).attr('data-day');
        filterHoursForGivenDay(month, day);
        let firstHourLink = jQuery('.hourLink:visible').first();
        firstHourLink.click();
        return false;
    });

    jQuery('.hourLink').click(function() {

        jQuery('.hourLink').css('font-weight', 'normal');
        jQuery(this).css('font-weight', 'bold');

        let month = jQuery(this).attr('data-month');
        let day = jQuery(this).attr('data-day');
        let hour = jQuery(this).attr('data-hour');
        filterSecondsForGivenHour(month, day, hour);

        let firstSecondLink = jQuery('.secondLink:visible').first();
        firstSecondLink.click();

        return false;
    });

    jQuery('.secondLink').click(function() {

        jQuery('.secondLink').css('font-weight', 'normal');
        jQuery(this).css('font-weight', 'bold');

        let idPhoto = parseInt(jQuery(this).attr('data-photo'));
        let photo = findPhoto(idPhoto);
        jQuery('#loading').show();
        jQuery('#photoDate').hide();
        loadPhoto(photo);
        return false;
    });
}

//----------------------------------------------------
// SHOW / HIDE WHOLE BROWSER
//----------------------------------------------------

function hideBrowser() {
    jQuery('#alertNoPhoto').show();
    jQuery('#browser').hide();
    jQuery('#controls').hide();
}

function showBrowser() {
    jQuery('#alertNoPhoto').hide();
    jQuery('#browser').show();
    jQuery('#controls').show();
}

//----------------------------------------------------
// RUNNING
//----------------------------------------------------

function getIdPhotoToDisplayAfterReset() {
    if (photos.length === 0) {
        return null;
    }
    if (cursor === photos.length -1) {
        return null;
    }
    return photos[cursor].id;
}

function installRetrievedPhotos(retrievedPhotos, idPhotoToDisplayAfterReset) {
    photos = retrievedPhotos;
    cursor = photos.length - 1;
    createCalendar();
    displayCalendar();

    let lastPhoto = photos[photos.length - 1];
    if (idPhotoToDisplayAfterReset !== null) {
        lastPhoto = findPhoto(idPhotoToDisplayAfterReset);
    }

    loadPhoto(lastPhoto);
    displayButtons();
    selectPhotoInCalendar(lastPhoto);

    listenEvents();
}

function reset(idPhotoToDisplayAfterReset) {
    //let idPhotoToDisplayAfterReset = getIdPhotoToDisplayAfterReset();
    jQuery.get(pathBrowseAllPhotos).done((retrievedPhotos) => {
        jQuery('#months, #days, #hours, #seconds').empty();
        cursor = 0;
        photos = [];
        calendar = [];
        if (retrievedPhotos.length === 0) {
            hideBrowser();
        } else {
            showBrowser();
            installRetrievedPhotos(retrievedPhotos, idPhotoToDisplayAfterReset);
        }

    });
}

jQuery('#reset').click(function() {
    reset(null);
    return false;
});

setInterval(function() {
    reset(getIdPhotoToDisplayAfterReset());
}, 30000);

reset(null);

