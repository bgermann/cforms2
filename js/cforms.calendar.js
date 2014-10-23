/*
 * Copyright (c) 2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

jQuery(function() {

    if( jQuery('.cf_date').length <= 0 ) {
        return;
    }

    jQuery('.cf_date').datepicker({
        buttonImage: cfCAL.ROOT + 'images/calendar.gif',
        changeYear: cfCAL.changeYear,
        buttonImageOnly: true,
        buttonText: cfCAL.TEXT_CHOOSE_DATE,
        showOn: 'both',
        dateFormat: cfCAL.dateFormat,
        dayNamesMin: cfCAL.dayNames,
        dayNamesShort: cfCAL.dayNames,
        monthNames: cfCAL.monthNames,
        firstDay: cfCAL.firstDayOfWeek,
        nextText: cfCAL.TEXT_NEXT_MONTH,
        prevText: cfCAL.TEXT_PREV_MONTH,
        closeText: cfCAL.TEXT_CLOSE
    });

    jQuery('#cforms_startdate').bind(
        'dpClosed',
        function(e, selectedDates) {
            var d = selectedDates[0];
            if (d) {
                d = new Date(d);
                jQuery('#cforms_enddate').dpSetStartDate(d.asString());
            }
        }
    );

    jQuery('#cforms_enddate').bind(
        'dpClosed',
        function(e, selectedDates) {
            var d = selectedDates[0];
            if (d) {
                d = new Date(d);
                jQuery('#cforms_startdate').dpSetEndDate(d.asString());
            }
        }
    );
});
