<?php

use function Livewire\Volt\{state};

state(['drawer' => false]);

?>

<x-layouts.app>
    @volt
        <div>
            <x-header title="Pomodoro Timer" size="text-3xl text-primary">
                <x-slot:actions>
                    <x-button label="" class="bg-gray-200" x-on:click="$wire.drawer = true" responsive
                        icon="o-adjustments-horizontal" />
                </x-slot:actions>
            </x-header>

            <x-drawer wire:model="drawer" title="Settings" right separator with-close-button class="w-11/12 lg:w-1/3">
                <div class="grid gap-5">
                    <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                        @keydown.enter="$wire.drawer = false" />
                </div>
                <x-slot:actions>
                    <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
                </x-slot:actions>
            </x-drawer>

            <div x-data="{
                focusMinutes: 2,
                focusSeconds: 0,
                focusTime: 0,
                breakMinutes: 1,
                breakSeconds: 0,
                breakTime: 0,
                remainingTime: 0,
                minutes: 0,
                seconds: 0,
                isRunning: false,
                isPause: false,
                timerInterval: null,
                progressMax: 0,
                progress: 0,
                mode: 'focus',
                startTimer: function() {
                    this.focusTime = this.focusMinutes * 60 + this.focusSeconds;
                    this.breakTime = this.breakMinutes * 60 + this.breakSeconds;

                    this.timerInterval = setInterval(() => {
                        this.onTimerTick();
                    }, 50);
                    this.isRunning = true;
                    this.isPause = false;
                },
                pauseTimer: function() {
                    clearInterval(this.timerInterval);
                    this.isRunning = false;
                    this.isPause = true;
                },
                stopTimer: function() {
                    this.pauseTimer();
                    this.isPause = false;
                    this.resetTimer();
                },
                onTimerTick: function() {
                    if (this.seconds <= 0) {
                        this.seconds = 59;
                        this.minutes--;
                    } else {
                        this.seconds--;
                    }

                    this.updateProgressBar();
                    this.clearTimerInterval();
                },
                updateProgressBar: function() {
                    this.remainingTime = this.minutes * 60 + this.seconds;
                    this.progressMax = this.mode === 'focus' ? this.focusTime : this.breakTime;
                    this.progress = this.progressMax - this.remainingTime;
                },
                clearTimerInterval: function() {
                    if (this.minutes === 0 && this.seconds === 0) {
                        clearInterval(this.timerInterval);
                        this.toggleMode();
                        this.startTimer();
                    }
                },
                toggleMode: function() {
                    this.mode = this.mode === 'focus' ? 'break' : 'focus';
                    this.stopTimer();
                },
                resetTimer: function() {
                    this.progress = 0;
                    if (this.mode === 'focus') {
                        this.minutes = this.focusMinutes;
                        this.seconds = this.focusSeconds;
                    } else {
                        this.minutes = this.breakMinutes;
                        this.seconds = this.breakSeconds;
                    }
                }
            }" x-init="resetTimer()">

                <div>
                    <x-progress x-bind:value="progress" x-bind:max="progressMax" class="h-2"
                        x-bind:class="mode === 'focus' ? 'progress-success' : 'progress-info'" />
                </div>

                <div class="flex flex-col items-center max-w-sm gap-10 p-6 mx-auto">
                    <div class="flex font-mono text-9xl">
                        <span x-text="minutes.toString().padStart(2, '0')"></span>
                        <span>:</span>
                        <span x-text="seconds.toString().padStart(2, '0')"></span>
                    </div>
                    <div>
                        <template x-if="mode === 'focus'">
                            <span class="text-xl text-gray-700 capitalize">
                                Stay focus for <span x-text="focusMinutes"></span> minutes
                            </span>
                        </template>
                        <template x-if="mode === 'break'">
                            <span class="text-xl text-gray-700 capitalize">
                                Take a break for <span x-text="breakMinutes"></span> minutes
                            </span>
                        </template>
                    </div>
                    <div class="flex items-center gap-8">
                        <div class="w-16">
                            <x-button class="w-16 h-16 btn-circle btn-error" x-cloak x-show="isRunning || isPause"
                                x-on:click="stopTimer()">
                                <x-icon name="s-stop" class="w-10 text-white" />
                            </x-button>
                        </div>
                        <x-button class="w-24 h-24 btn-circle btn-primary" x-cloak x-show="!isRunning"
                            x-on:click="startTimer()">
                            <x-icon name="s-play" class="w-16 text-white" />
                        </x-button>
                        <x-button class="w-24 h-24 btn-circle btn-warning" x-cloak x-show="isRunning"
                            x-on:click="pauseTimer()">
                            <x-icon name="s-pause" class="w-16 text-white" />
                        </x-button>
                        <x-button class="w-16 h-16 btn-circle btn-secondary">
                            <x-icon name="s-chevron-double-right" class="w-10 text-white" x-cloak
                                x-on:click="toggleMode()" />
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.app>
