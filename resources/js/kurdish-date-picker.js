function startOfDay(d) {
    return new Date(d.getFullYear(), d.getMonth(), d.getDate())
}

function diffDays(a, b) {
    return Math.round((startOfDay(a) - startOfDay(b)) / 86400000)
}

function nawrozInstant(gregorianYear, nm, nd) {
    return new Date(gregorianYear, nm - 1, nd)
}

function monthLengthsForSpan(totalDays, base) {
    const lengths = [...base]
    const sum = lengths.reduce((x, y) => x + y, 0)
    lengths[11] += totalDays - sum
    if (lengths[11] < 1) {
        throw new Error('Invalid Kurdish month lengths for this year span.')
    }
    return lengths
}

function fromGregorianDate(y, m, d, rules) {
    const nm = rules.nawrozMonth
    const nd = rules.nawrozDay
    const g = new Date(y, m - 1, d)
    const nawrozThisGy = nawrozInstant(y, nm, nd)
    let gregorianCycleYear
    let yearStart
    if (g < nawrozThisGy) {
        gregorianCycleYear = y - 1
        yearStart = nawrozInstant(y - 1, nm, nd)
    } else {
        gregorianCycleYear = y
        yearStart = nawrozThisGy
    }
    const yearEnd = new Date(yearStart)
    yearEnd.setFullYear(yearEnd.getFullYear() + 1)
    const totalDays = diffDays(yearEnd, yearStart)
    const lengths = monthLengthsForSpan(totalDays, rules.monthLengths)
    const dayIndex = diffDays(g, yearStart)
    if (dayIndex < 0 || dayIndex >= totalDays) {
        throw new Error('Date outside Kurdish year span.')
    }
    let remaining = dayIndex
    for (let month = 1; month <= 12; month++) {
        const len = lengths[month - 1]
        if (remaining < len) {
            return {
                year: gregorianCycleYear + rules.yearOffset,
                month,
                day: remaining + 1,
            }
        }
        remaining -= len
    }
    throw new Error('Could not map Kurdish date.')
}

function addDays(date, n) {
    const x = new Date(date)
    x.setDate(x.getDate() + n)
    return x
}

function toGregorianDate(ky, km, kd, rules) {
    const gregorianCycleYear = ky - rules.yearOffset
    const yearStart = nawrozInstant(
        gregorianCycleYear,
        rules.nawrozMonth,
        rules.nawrozDay,
    )
    const yearEnd = new Date(yearStart)
    yearEnd.setFullYear(yearEnd.getFullYear() + 1)
    const totalDays = diffDays(yearEnd, yearStart)
    const lengths = monthLengthsForSpan(totalDays, rules.monthLengths)
    if (km < 1 || km > 12) {
        throw new Error('Invalid month')
    }
    if (kd < 1 || kd > lengths[km - 1]) {
        throw new Error('Invalid day')
    }
    let offset = 0
    for (let m = 1; m < km; m++) {
        offset += lengths[m - 1]
    }
    offset += kd - 1
    return addDays(yearStart, offset)
}

function formatYmd(d) {
    const y = d.getFullYear()
    const m = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    return `${y}-${m}-${day}`
}

function pad2(n) {
    return String(n ?? 0).padStart(2, '0')
}

function formatKurdishDisplay(format, k, monthNames, hour, minute, second, hasTime) {
    let out = ''
    for (let i = 0; i < format.length; i++) {
        if (format[i] === '\\' && i + 1 < format.length) {
            out += format[++i]
            continue
        }
        const c = format[i]
        if (c === 'Y') {
            out += String(k.year).padStart(4, '0')
        } else if (c === 'y') {
            out += String(k.year).slice(-2)
        } else if (c === 'm') {
            out += pad2(k.month)
        } else if (c === 'n') {
            out += String(k.month)
        } else if (c === 'd') {
            out += pad2(k.day)
        } else if (c === 'j') {
            out += String(k.day)
        } else if (c === 'F') {
            out += monthNames[k.month - 1] ?? ''
        } else if (hasTime && c === 'H') {
            out += pad2(hour)
        } else if (hasTime && c === 'h') {
            const h = hour % 12 || 12
            out += String(h)
        } else if (hasTime && c === 'i') {
            out += pad2(minute)
        } else if (hasTime && c === 's') {
            out += pad2(second)
        } else if (hasTime && c === 'G') {
            out += String(hour ?? 0)
        } else if (hasTime && c === 'g') {
            out += String(hour % 12 || 12)
        } else {
            out += c
        }
    }
    return out
}

/**
 * Labels must be Sunday→Saturday (indices 0–6, matching Date.getDay()).
 * Rotate so column headers align with firstDayOfWeek (0=Sun … 6=Sat).
 */
function rotateDayLabels(labels, firstDayOfWeek) {
    const f = firstDayOfWeek ?? 0
    if (f === 0) {
        return [...labels]
    }
    return [...labels.slice(f), ...labels.slice(0, f)]
}

function monthLengthFor(ky, km, rules) {
    const gregorianCycleYear = ky - rules.yearOffset
    const yearStart = nawrozInstant(
        gregorianCycleYear,
        rules.nawrozMonth,
        rules.nawrozDay,
    )
    const yearEnd = new Date(yearStart)
    yearEnd.setFullYear(yearEnd.getFullYear() + 1)
    const totalDays = diffDays(yearEnd, yearStart)
    const lengths = monthLengthsForSpan(totalDays, rules.monthLengths)
    return lengths[km - 1]
}

export default function kurdishDatePickerFormComponent({
    rules,
    monthNames,
    dayLabels: dayLabelsSundayFirst,
    displayFormat,
    firstDayOfWeek,
    isAutofocused,
    shouldCloseOnDateSelection,
    defaultFocusedDate,
    state,
    hasTime,
    hasSeconds,
}) {
    return {
        state,
        rules,
        monthNames,
        displayText: '',
        displayFormat,
        firstDayOfWeek: firstDayOfWeek ?? 1,
        shouldCloseOnDateSelection,
        defaultFocusedDate,
        hasTime,
        hasSeconds,
        /** Sunday→Saturday, unrotated */
        dayLabelsRaw: [],
        /** Rotated to match firstDayOfWeek */
        dayLabels: [],
        focusedKurdishYear: null,
        focusedKurdishMonth: 1,
        focusedKurdishDay: 1,
        /** Custom month UI (Filament-style select, not native `<select>`) */
        monthDropdownOpen: false,
        daysInFocusedMonth: [],
        emptyDaysInFocusedMonth: [],
        hour: 0,
        minute: 0,
        second: 0,
        isClearingState: false,

        init() {
            this.dayLabelsRaw = Array.isArray(dayLabelsSundayFirst)
                ? [...dayLabelsSundayFirst]
                : []
            this.dayLabels = rotateDayLabels(
                this.dayLabelsRaw,
                this.firstDayOfWeek,
            )

            this.$watch('state', () => this.syncFromState())
            this.$watch('focusedKurdishMonth', () => {
                this.clampDayInMonth()
                this.setupDaysGrid()
            })
            this.$watch('focusedKurdishYear', () => {
                this.clampDayInMonth()
                this.setupDaysGrid()
            })
            this.initTimeWatchers()
            this.$nextTick(() => {
                this.syncFromState()
                if (isAutofocused) {
                    this.togglePanelVisibility(this.$refs.button)
                }
            })
        },

        initTimeWatchers() {
            if (!this.hasTime) {
                return
            }
            this.$watch('hour', () => this.applyTimeToState())
            this.$watch('minute', () => this.applyTimeToState())
            this.$watch('second', () => this.applyTimeToState())
        },

        clampDayInMonth() {
            const ky = +this.focusedKurdishYear
            const km = +this.focusedKurdishMonth
            if (!Number.isFinite(ky) || !Number.isFinite(km)) {
                return
            }
            try {
                const len = monthLengthFor(ky, km, this.rules)
                if (this.focusedKurdishDay > len) {
                    this.focusedKurdishDay = len
                }
                if (this.focusedKurdishDay < 1) {
                    this.focusedKurdishDay = 1
                }
            } catch {
                /* noop */
            }
        },

        syncFromState() {
            const now = new Date()
            const defaultK = fromGregorianDate(
                now.getFullYear(),
                now.getMonth() + 1,
                now.getDate(),
                this.rules,
            )

            if (
                this.state === undefined ||
                this.state === null ||
                this.state === ''
            ) {
                this.displayText = ''
                this.focusedKurdishYear = defaultK.year
                this.focusedKurdishMonth = defaultK.month
                this.focusedKurdishDay = defaultK.day
                this.hour = 0
                this.minute = 0
                this.second = 0
                this.setupDaysGrid()
                return
            }

            const raw = String(this.state).trim()
            const [datePart, timePart] = raw.includes(' ')
                ? raw.split(' ')
                : [raw, null]
            const ymd = datePart.split('-').map(Number)
            const g = new Date(ymd[0], ymd[1] - 1, ymd[2])
            if (timePart) {
                const hms = timePart.split(':')
                this.hour = parseInt(hms[0] ?? 0, 10) || 0
                this.minute = parseInt(hms[1] ?? 0, 10) || 0
                this.second = parseInt((hms[2] ?? '0').slice(0, 2), 10) || 0
            } else {
                this.hour = 0
                this.minute = 0
                this.second = 0
            }

            const k = fromGregorianDate(
                g.getFullYear(),
                g.getMonth() + 1,
                g.getDate(),
                this.rules,
            )
            this.focusedKurdishYear = k.year
            this.focusedKurdishMonth = k.month
            this.focusedKurdishDay = k.day
            this.setDisplayText()
            this.setupDaysGrid()
        },

        setDisplayText() {
            if (
                this.state === undefined ||
                this.state === null ||
                this.state === ''
            ) {
                this.displayText = ''
                return
            }
            const raw = String(this.state).trim()
            const [datePart, timePart] = raw.includes(' ')
                ? raw.split(' ')
                : [raw, null]
            const ymd = datePart.split('-').map(Number)
            const g = new Date(ymd[0], ymd[1] - 1, ymd[2])
            let h = this.hour
            let m = this.minute
            let s = this.second
            if (timePart && this.hasTime) {
                const hms = timePart.split(':')
                h = parseInt(hms[0] ?? 0, 10) || 0
                m = parseInt(hms[1] ?? 0, 10) || 0
                s = parseInt((hms[2] ?? '0').slice(0, 2), 10) || 0
            }
            const k = fromGregorianDate(
                g.getFullYear(),
                g.getMonth() + 1,
                g.getDate(),
                this.rules,
            )
            this.displayText = formatKurdishDisplay(
                this.displayFormat,
                k,
                this.monthNames,
                h,
                m,
                s,
                this.hasTime,
            )
        },

        setupDaysGrid() {
            const ky = +this.focusedKurdishYear
            const km = +this.focusedKurdishMonth
            if (!Number.isFinite(ky) || !Number.isFinite(km)) {
                return
            }
            let g1
            try {
                g1 = toGregorianDate(ky, km, 1, this.rules)
            } catch {
                this.daysInFocusedMonth = []
                this.emptyDaysInFocusedMonth = []
                return
            }
            const weekday = g1.getDay()
            const empty = (weekday - this.firstDayOfWeek + 7) % 7
            this.emptyDaysInFocusedMonth = Array.from(
                { length: empty },
                (_, i) => i,
            )
            let len
            try {
                len = monthLengthFor(ky, km, this.rules)
            } catch {
                this.daysInFocusedMonth = []
                return
            }
            this.daysInFocusedMonth = Array.from({ length: len }, (_, i) => i + 1)
        },

        getSelectedKurdish() {
            if (
                this.state === undefined ||
                this.state === null ||
                this.state === ''
            ) {
                return null
            }
            const raw = String(this.state).trim()
            const datePart = raw.split(/[\sT]/)[0]
            const ymd = datePart.split('-').map(Number)
            const g = new Date(ymd[0], ymd[1] - 1, ymd[2])
            return fromGregorianDate(
                g.getFullYear(),
                g.getMonth() + 1,
                g.getDate(),
                this.rules,
            )
        },

        dayIsSelected(day) {
            const sel = this.getSelectedKurdish()
            if (!sel) {
                return false
            }
            return (
                sel.year === +this.focusedKurdishYear &&
                sel.month === +this.focusedKurdishMonth &&
                sel.day === day
            )
        },

        dayIsToday(day) {
            const now = new Date()
            const k = fromGregorianDate(
                now.getFullYear(),
                now.getMonth() + 1,
                now.getDate(),
                this.rules,
            )
            return (
                k.year === +this.focusedKurdishYear &&
                k.month === +this.focusedKurdishMonth &&
                k.day === day
            )
        },

        dayIsDisabled(day) {
            let g
            try {
                g = toGregorianDate(
                    +this.focusedKurdishYear,
                    +this.focusedKurdishMonth,
                    day,
                    this.rules,
                )
            } catch {
                return true
            }
            const ds = formatYmd(g)
            if (this.$refs?.minDate?.value) {
                const min = this.$refs.minDate.value
                if (min && ds < String(min).slice(0, 10)) {
                    return true
                }
            }
            if (this.$refs?.maxDate?.value) {
                const max = this.$refs.maxDate.value
                if (max && ds > String(max).slice(0, 10)) {
                    return true
                }
            }
            return false
        },

        selectDate(day) {
            if (this.dayIsDisabled(day)) {
                return
            }
            let g
            try {
                g = toGregorianDate(
                    +this.focusedKurdishYear,
                    +this.focusedKurdishMonth,
                    day,
                    this.rules,
                )
            } catch {
                return
            }
            this.focusedKurdishDay = day
            this.setStateFromGregorian(g)
            if (this.shouldCloseOnDateSelection && !this.hasTime) {
                this.togglePanelVisibility()
            }
        },

        setFocusedDay(day) {
            this.focusedKurdishDay = day
        },

        setStateFromGregorian(g) {
            if (this.dateIsDisabledGregorian(g)) {
                return
            }
            if (!this.hasTime) {
                this.state = formatYmd(g)
            } else if (this.hasSeconds) {
                this.state = `${formatYmd(g)} ${pad2(this.hour)}:${pad2(this.minute)}:${pad2(this.second)}`
            } else {
                this.state = `${formatYmd(g)} ${pad2(this.hour)}:${pad2(this.minute)}:00`
            }
            this.setDisplayText()
        },

        applyTimeToState() {
            if (this.isClearingState) {
                return
            }
            const sel = this.getSelectedKurdish()
            if (!sel) {
                return
            }
            let g
            try {
                g = toGregorianDate(sel.year, sel.month, sel.day, this.rules)
            } catch {
                return
            }
            this.setStateFromGregorian(g)
        },

        dateIsDisabledGregorian(g) {
            const ds = formatYmd(g)
            if (this.$refs?.minDate?.value) {
                const min = this.$refs.minDate.value
                if (min && ds < String(min).slice(0, 10)) {
                    return true
                }
            }
            if (this.$refs?.maxDate?.value) {
                const max = this.$refs.maxDate.value
                if (max && ds > String(max).slice(0, 10)) {
                    return true
                }
            }
            return false
        },

        clearState() {
            this.isClearingState = true
            this.state = null
            this.displayText = ''
            this.$nextTick(() => {
                this.isClearingState = false
            })
        },

        onEscapeInPicker($event) {
            if (this.monthDropdownOpen) {
                this.monthDropdownOpen = false
                $event.stopPropagation()
                return
            }
            if (this.isOpen()) {
                $event.stopPropagation()
            }
        },

        toggleMonthDropdown() {
            this.monthDropdownOpen = !this.monthDropdownOpen
        },

        selectKurdishMonth(month) {
            this.focusedKurdishMonth = month
            this.monthDropdownOpen = false
        },

        togglePanelVisibility() {
            this.monthDropdownOpen = false
            if (!this.isOpen()) {
                this.setupDaysGrid()
            }
            this.$refs.panel?.toggle?.(this.$refs.button)
        },

        isOpen() {
            return this.$refs.panel?.style?.display === 'block'
        },
    }
}
