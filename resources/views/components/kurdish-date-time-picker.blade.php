@php
    $fieldWrapperView = $getFieldWrapperView();
    $datalistOptions = $getDatalistOptions();
    $disabledDates = $getDisabledDates();
    $extraAlpineAttributes = $getExtraAlpineAttributes();
    $extraAttributeBag = $getExtraAttributeBag();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $hasDate = $hasDate();
    $hasTime = $hasTime();
    $hasSeconds = $hasSeconds();
    $id = $getId();
    $isDisabled = $isDisabled();
    $isAutofocused = $isAutofocused();
    $isPrefixInline = $isPrefixInline();
    $isSuffixInline = $isSuffixInline();
    $maxDate = $getMaxDate();
    $minDate = $getMinDate();
    $defaultFocusedDate = $getDefaultFocusedDate();
    $prefixActions = $getPrefixActions();
    $prefixIcon = $getPrefixIcon();
    $prefixIconColor = $getPrefixIconColor();
    $prefixLabel = $getPrefixLabel();
    $suffixActions = $getSuffixActions();
    $suffixIcon = $getSuffixIcon();
    $suffixIconColor = $getSuffixIconColor();
    $suffixLabel = $getSuffixLabel();
    $statePath = $getStatePath();
    $placeholder = $getPlaceholder();
    $isReadOnly = $isReadOnly();
    $isRequired = $isRequired();
    $isConcealed = $isConcealed();
    $step = $getStep();
    $type = $getType();
    $livewireKey = $getLivewireKey();

    $kurdishRules = [
        'yearOffset' => \Rawand\FilamentKurdishCalendar\Support\KurdishCalendarConverter::YEAR_OFFSET,
        'nawrozMonth' => \Rawand\FilamentKurdishCalendar\Support\KurdishCalendarConverter::NAWROZ_MONTH,
        'nawrozDay' => \Rawand\FilamentKurdishCalendar\Support\KurdishCalendarConverter::NAWROZ_DAY,
        'monthLengths' => \Rawand\FilamentKurdishCalendar\Support\KurdishCalendarConverter::MONTH_LENGTHS_BASE,
    ];
@endphp

@once('rawand-filament-kurdish-calendar-picker-layout')
    <style>
        .fi-kurdish-calendar-picker .fi-fo-date-time-picker-panel-header {
            overflow: visible;
        }

        .fi-kurdish-calendar-picker .fi-select-input .fi-kurdish-month-dropdown.fi-dropdown-panel {
            z-index: 30;
            width: max-content !important;
            min-width: max(100%, 17rem) !important;
            max-width: min(22rem, calc(100vw - 2rem)) !important;
            overflow-x: hidden;
            overflow-y: auto;
            max-height: min(22rem, 70vh);
            scrollbar-width: thin;
            scrollbar-color: rgb(148 163 184 / 0.65) transparent;
        }

        .fi-kurdish-calendar-picker .fi-kurdish-month-dropdown.fi-dropdown-panel::-webkit-scrollbar {
            width: 6px;
        }

        .fi-kurdish-calendar-picker .fi-kurdish-month-dropdown.fi-dropdown-panel::-webkit-scrollbar-thumb {
            border-radius: 9999px;
            background-color: rgb(148 163 184 / 0.7);
        }

        .dark .fi-kurdish-calendar-picker .fi-kurdish-month-dropdown.fi-dropdown-panel {
            scrollbar-color: rgb(100 116 139 / 0.65) transparent;
        }

        .dark .fi-kurdish-calendar-picker .fi-kurdish-month-dropdown.fi-dropdown-panel::-webkit-scrollbar-thumb {
            background-color: rgb(100 116 139 / 0.65);
        }
    </style>
@endonce

<x-dynamic-component
    :component="$fieldWrapperView"
    :field="$field"
    :inline-label-vertical-alignment="\Filament\Support\Enums\VerticalAlignment::Center"
>
    <x-filament::input.wrapper
        :disabled="$isDisabled"
        :inline-prefix="$isPrefixInline"
        :inline-suffix="$isSuffixInline"
        :prefix="$prefixLabel"
        :prefix-actions="$prefixActions"
        :prefix-icon="$prefixIcon"
        :prefix-icon-color="$prefixIconColor"
        :suffix="$suffixLabel"
        :suffix-actions="$suffixActions"
        :suffix-icon="$suffixIcon"
        :suffix-icon-color="$suffixIconColor"
        :valid="! $errors->has($statePath)"
        x-on:focus-input.stop="$el.querySelector('input:not([type=hidden])')?.focus()"
        :attributes="\Filament\Support\prepare_inherited_attributes($extraAttributeBag)->class(['fi-fo-date-time-picker', 'fi-kurdish-calendar-picker'])"
    >
        @if ($isNative())
            <input
                {{
                    $extraInputAttributeBag
                        ->merge($extraAlpineAttributes, escape: false)
                        ->merge([
                            'autofocus' => $isAutofocused,
                            'disabled' => $isDisabled,
                            'id' => $id,
                            'list' => $datalistOptions ? $id . '-list' : null,
                            'max' => $hasTime ? $maxDate : ($maxDate ? \Carbon\Carbon::parse($maxDate)->toDateString() : null),
                            'min' => $hasTime ? $minDate : ($minDate ? \Carbon\Carbon::parse($minDate)->toDateString() : null),
                            'placeholder' => filled($placeholder) ? e($placeholder) : null,
                            'readonly' => $isReadOnly,
                            'required' => $isRequired && (! $isConcealed),
                            'step' => $step,
                            'type' => $type,
                            $applyStateBindingModifiers('wire:model') => $statePath,
                            'x-data' => count($extraAlpineAttributes) ? '{}' : null,
                        ], escape: false)
                        ->class([
                            'fi-input',
                            'fi-input-has-inline-prefix' => $isPrefixInline && (count($prefixActions) || $prefixIcon || filled($prefixLabel)),
                            'fi-input-has-inline-suffix' => $isSuffixInline && (count($suffixActions) || $suffixIcon || filled($suffixLabel)),
                        ])
                }}
            />
        @else
            <div
                x-load
                x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('kurdish-date-picker', 'rawand201/filament-kurdish-calendar') }}"
                x-data="kurdishDatePickerFormComponent({
                    rules: @js($kurdishRules),
                    monthNames: @js(array_values(__('filament-kurdish-calendar::months'))),
                    dayLabels: @js(__('filament-kurdish-calendar::days.short')),
                    displayFormat: @js($getDisplayFormat()),
                    firstDayOfWeek: {{ $getFirstDayOfWeek() ?? 1 }},
                    isAutofocused: @js($isAutofocused),
                    shouldCloseOnDateSelection: @js($shouldCloseOnDateSelection()),
                    defaultFocusedDate: @js($defaultFocusedDate),
                    state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$statePath}')") }},
                    hasTime: @js($hasTime),
                    hasSeconds: @js($hasSeconds),
                })"
                wire:ignore
                wire:key="{{ $livewireKey }}.{{
                    substr(md5(serialize([
                        $disabledDates,
                        $isDisabled,
                        $isReadOnly,
                        $maxDate,
                        $minDate,
                        $hasDate,
                        $hasTime,
                        $hasSeconds,
                    ])), 0, 64)
                }}"
                x-on:keydown.esc="onEscapeInPicker($event)"
                {{ $getExtraAlpineAttributeBag() }}
            >
                <input x-ref="maxDate" type="hidden" value="{{ $maxDate }}" />

                <input x-ref="minDate" type="hidden" value="{{ $minDate }}" />

                <input
                    x-ref="disabledDates"
                    type="hidden"
                    value="{{ json_encode($disabledDates) }}"
                />

                <button
                    x-ref="button"
                    x-on:click="togglePanelVisibility()"
                    aria-label="{{ $placeholder }}"
                    type="button"
                    tabindex="-1"
                    @disabled($isDisabled || $isReadOnly)
                    {{
                        $getExtraTriggerAttributeBag()->class([
                            'fi-fo-date-time-picker-trigger',
                        ])
                    }}
                >
                    <input
                        @disabled($isDisabled)
                        readonly
                        placeholder="{{ $placeholder }}"
                        wire:key="{{ $livewireKey }}.display-text"
                        x-model="displayText"
                        @if ($id = $getId()) id="{{ $id }}" @endif
                        @class([
                            'fi-fo-date-time-picker-display-text-input',
                        ])
                    />
                </button>

                <div
                    x-ref="panel"
                    x-cloak
                    x-float.placement.bottom-start.offset.flip.shift="{ offset: 8 }"
                    wire:ignore
                    wire:key="{{ $livewireKey }}.panel"
                    @class([
                        'fi-fo-date-time-picker-panel',
                    ])
                >
                    @if ($hasDate)
                        <div class="fi-fo-date-time-picker-panel-header">
                            {{-- Filament non-native Select styling (same classes as filament/forms select.js) --}}
                            <div
                                class="fi-select-input min-w-0 grow"
                                x-on:click.outside="monthDropdownOpen = false"
                            >
                                <div class="fi-select-input-ctn">
                                    <button
                                        type="button"
                                        class="fi-select-input-btn"
                                        x-on:click.stop="toggleMonthDropdown()"
                                        x-bind:aria-expanded="monthDropdownOpen"
                                        aria-haspopup="listbox"
                                        @disabled($isDisabled || $isReadOnly)
                                    >
                                        <div class="fi-select-input-value-ctn">
                                            <span
                                                class="fi-select-input-value-label"
                                                x-text="monthNames[focusedKurdishMonth - 1]"
                                            ></span>
                                        </div>
                                    </button>

                                    <div
                                        x-show="monthDropdownOpen"
                                        x-transition.opacity.200ms
                                        x-cloak
                                        class="fi-dropdown-panel fi-kurdish-month-dropdown start-0 top-full mt-1"
                                        role="listbox"
                                        x-bind:aria-hidden="!monthDropdownOpen"
                                    >
                                        <ul
                                            class="fi-select-input-options-ctn m-0 list-none p-0"
                                            role="none"
                                        >
                                            <template
                                                x-for="(name, index) in monthNames"
                                                :key="index"
                                            >
                                                <li
                                                    role="option"
                                                    x-bind:aria-selected="+focusedKurdishMonth === index + 1"
                                                    x-on:click.stop="selectKurdishMonth(index + 1)"
                                                    x-bind:class="{
                                                        'fi-selected': +focusedKurdishMonth === index + 1,
                                                    }"
                                                    class="fi-dropdown-list-item fi-select-input-option whitespace-nowrap text-start"
                                                >
                                                    <span x-text="name"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <input
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="focusedKurdishYear"
                                class="fi-fo-date-time-picker-year-input"
                            />
                        </div>

                        <div class="fi-fo-date-time-picker-calendar-header">
                            <template
                                x-for="(day, index) in dayLabels"
                                x-bind:key="index"
                            >
                                <div
                                    x-text="day"
                                    class="fi-fo-date-time-picker-calendar-header-day"
                                ></div>
                            </template>
                        </div>

                        <div
                            role="grid"
                            class="fi-fo-date-time-picker-calendar"
                        >
                            <template
                                x-for="(pad, index) in emptyDaysInFocusedMonth"
                                x-bind:key="'e' + index"
                            >
                                <div></div>
                            </template>

                            <template
                                x-for="day in daysInFocusedMonth"
                                x-bind:key="day"
                            >
                                <div
                                    x-text="day"
                                    x-on:click="dayIsDisabled(day) || selectDate(day)"
                                    x-on:mouseenter="setFocusedDay(day)"
                                    role="option"
                                    x-bind:aria-selected="focusedKurdishDay === day"
                                    x-bind:class="{
                                        'fi-fo-date-time-picker-calendar-day-today': dayIsToday(day),
                                        'fi-focused': focusedKurdishDay === day,
                                        'fi-selected': dayIsSelected(day),
                                        'fi-disabled': dayIsDisabled(day),
                                    }"
                                    class="fi-fo-date-time-picker-calendar-day"
                                ></div>
                            </template>
                        </div>
                    @endif

                    @if ($hasTime)
                        <div class="fi-fo-date-time-picker-time-inputs">
                            <input
                                max="23"
                                min="0"
                                step="{{ $getHoursStep() }}"
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="hour"
                            />

                            <span
                                class="fi-fo-date-time-picker-time-input-separator"
                            >
                                :
                            </span>

                            <input
                                max="59"
                                min="0"
                                step="{{ $getMinutesStep() }}"
                                type="number"
                                inputmode="numeric"
                                x-model.debounce="minute"
                            />

                            @if ($hasSeconds)
                                <span
                                    class="fi-fo-date-time-picker-time-input-separator"
                                >
                                    :
                                </span>

                                <input
                                    max="59"
                                    min="0"
                                    step="{{ $getSecondsStep() }}"
                                    type="number"
                                    inputmode="numeric"
                                    x-model.debounce="second"
                                />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </x-filament::input.wrapper>

    @if ($datalistOptions)
        <datalist id="{{ $id }}-list">
            @foreach ($datalistOptions as $option)
                <option value="{{ $option }}" />
            @endforeach
        </datalist>
    @endif
</x-dynamic-component>
