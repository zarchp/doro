<?php

use function Livewire\Volt\{state};

state([
    'drawerSettings' => false,
    'drawerTasks' => false,
]);

?>

<x-layouts.app>
    @volt
        <div x-data="{
            focusMinutes: $persist(25),
            focusSeconds: $persist(0),
            focusTime: $persist(0),
            breakMinutes: $persist(5),
            breakSeconds: $persist(0),
            breakTime: $persist(0),
            longMinutes: $persist(15),
            longSeconds: $persist(0),
            longTime: $persist(0),
            longInterval: $persist(3),
            currentInterval: $persist(1),
            remainingTime: $persist(0),
            minutes: $persist(0),
            seconds: $persist(0),
            isAutoStartFocus: $persist(false),
            isAutoStartBreaks: $persist(false),
            isRunning: $persist(false),
            isPause: $persist(false),
            timerInterval: $persist(null),
            progressMax: $persist(0),
            progressColor: $persist('progress-success'),
            mode: $persist('focus'),
            taskDescription: '',
            activeTasks: [],
            tasks: $persist([{
                    'id': 1,
                    'description': 'My first task',
                    'isDone': false,
                },
                {
                    'id': 2,
                    'description': 'Second task',
                    'isDone': false,
                },
            ]),
            resortTasks: (item, position) => {
                const index = $data.tasks.findIndex(element => element.id == item);

                if (index !== -1) {
                    const removedItem = $data.tasks.splice(index, 1)[0];
                    console.log(removedItem);
                    $data.tasks.splice(position, 0, removedItem);
                    return false;
                }
            },
            createTask: function() {
                this.tasks.push({
                    'id': this.tasks.length + 1,
                    'description': this.taskDescription,
                    'isDone': false,
                });
                this.taskDescription = '';
            },
            deleteTask: function(index) {
                $data.tasks.splice(index, 1);
                {{-- $data.tasks.forEach(function(task, index) {
                    $data.tasks[index].order = index + 1;
                }); --}}
            },
            addTask: function() {
                $wire.drawerTasks = true;
                setTimeout(() => document.getElementsByClassName('inputDescription')[0].focus(), 200);
            },
            setActiveTasks: function() {
                this.activeTasks = this.tasks.filter(task => task.isDone === false);
            },
            startTimer: function() {
                this.focusTime = this.focusMinutes * 60 + this.focusSeconds || 0;
                this.breakTime = this.breakMinutes * 60 + this.breakSeconds || 0;
                this.longTime = this.longMinutes * 60 + this.longSeconds || 0;

                this.timerInterval = setInterval(() => {
                    this.onTimerTick();
                }, 5);
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
                this.progressMax = {
                    focus: this.focusTime,
                    break: this.breakTime,
                    long: this.longTime
                } [this.mode];
                this.progress = this.progressMax - this.remainingTime;
            },
            clearTimerInterval: function() {
                if (this.minutes === 0 && this.seconds === 0) {
                    clearInterval(this.timerInterval);
                    this.toggleMode();

                    if (this.mode === 'focus' ? this.isAutoStartFocus : this.isAutoStartBreaks) {
                        this.startTimer();
                    }
                }
            },
            toggleMode: function() {
                modes = ['focus', 'break', 'long'];

                {{-- if (this.mode === 'focus') {
                    this.mode = this.currentInterval < this.longInterval ? 'break' : 'long';
                } else if (this.mode === 'break') {
                    this.mode = 'focus';
                    this.currentInterval += 1;
                } else if (this.mode === 'long') {
                    this.mode = 'focus';
                    this.currentInterval = 1;
                } --}}

                if (this.mode === 'focus') {
                    this.mode = this.currentInterval < this.longInterval ? 'break' : 'long';
                } else {
                    this.currentInterval = this.mode === 'break' ? this.currentInterval + 1 : 1;
                    this.mode = 'focus';
                }

                this.progressColor = {
                    focus: 'progress-success',
                    break: 'progress-info',
                    long: 'progress-error'
                } [this.mode];

                this.stopTimer();
            },
            resetTimer: function() {
                this.progress = 0;
                [this.minutes, this.seconds] = {
                    focus: [this.focusMinutes, this.focusSeconds],
                    break: [this.breakMinutes, this.breakSeconds],
                    long: [this.longMinutes, this.longSeconds]
                } [this.mode];
            },
            updateSettings: function(key, value) {
                if (this.mode === key) {
                    this.minutes = value;
                }
            }
        }" x-init="stopTimer();
        resetTimer();
        setActiveTasks();
        $watch('tasks', () => setActiveTasks())">
            <x-header title="Pomodoro Timer" size="text-3xl text-primary">
                <x-slot:actions>
                    <x-theme-toggle class="btn" title="Toggle Theme" />
                    <x-button label="" class="" x-on:click="$wire.drawerTasks = true" responsive
                        icon="o-document-check" title="Task" />
                    <x-button label="" class="" x-on:click="$wire.drawerSettings = true" responsive
                        icon="o-adjustments-horizontal" title="Settings" />
                </x-slot:actions>
            </x-header>

            <x-drawer wire:model="drawerSettings" title="Settings" right separator with-close-button
                class="w-10/12 lg:w-1/4">
                <div class="grid gap-6">
                    <div class="grid gap-4">
                        {{-- <x-icon name="o-clock" label="Timer" class="text-lg font-bold text-gray-400" /> --}}

                        <div class="grid gap-2">
                            <div class="font-bold">Time (minutes)</div>
                            <div class="grid grid-cols-3 gap-4">
                                <x-input label="Focus" type="number" min="1" max="60" x-model="focusMinutes"
                                    x-on:input="updateSettings('focus', $event.target.value)" />
                                <x-input label="Break" type="number" min="1" max="15" x-model="breakMinutes"
                                    x-on:input="updateSettings('break', $event.target.value)" />
                                <x-input label="Long Break" type="number" min="5" max="60"
                                    x-model="longMinutes" x-on:input="updateSettings('long', $event.target.value)" />
                            </div>
                        </div>

                        <div class="grid gap-4">
                            <div class="flex items-center gap-4">
                                <div class="grow">
                                    <x-range min="1" max="10" step="1" label="Long Break interval"
                                        hint="" class="range-primary" x-model="longInterval"
                                        x-on:input="currentInterval= 1" />
                                </div>
                                <div x-text="longInterval" class="pt-4"></div>
                            </div>
                            <x-toggle label="Auto Start Focus" x-model="isAutoStartFocus" right hint="" />
                            <x-toggle label="Auto Start Breaks" x-model="isAutoStartBreaks" right hint="" />
                        </div>
                    </div>
                </div>
            </x-drawer>

            <x-drawer wire:model="drawerTasks" title="Tasks" right separator with-close-button class="w-10/12 lg:w-1/4">
                <div class="grid gap-6">
                    <div class="grid gap-4">
                        <x-input label="Enter something" x-model="taskDescription" inline x-on:keyup.enter="createTask()"
                            class="inputDescription" />

                        <ul class="flex flex-col gap-2" x-sort="resortTasks">
                            <template x-for="(task, index) in tasks" x-bind:key="task.id">
                                <li class="flex items-center gap-2 p-2 rounded-lg cursor-move bg-base-300"
                                    x-bind:class="task.isDone ? 'opacity-50' : ''" x-sort:item="task.id">
                                    <div class="w-4" x-sort:handle>
                                        <x-icon name="o-bars-2" />
                                    </div>
                                    <div x-text="task.description" class=" grow"
                                        x-bind:class="task.isDone ? 'line-through text-neutral-content' : ''">
                                    </div>
                                    <x-button icon="o-trash"
                                        class="bg-transparent border-transparent text-error btn-sm btn-circle hover:text-white hover:bg-red-600"
                                        x-on:click="deleteTask(index)" />
                                    <x-checkbox class="checkbox" x-model="task.isDone" />
                                </li>
                            </template>
                        </ul>

                        {{-- <ul class="flex flex-col mt-4">
                            <template x-for="(task, index) in tasks" x-bind:key="task.id">
                                <li class="flex items-center gap-2" x-bind:class="task.isDone ? 'opacity-50' : ''">
                                    -
                                    <span x-text="task.description" class=" grow"
                                        x-bind:class="task.isDone ? 'line-through text-neutral-content' : ''">
                                    </span>
                                </li>
                            </template>
                        </ul> --}}
                    </div>
                </div>
            </x-drawer>

            <div>
                <div>
                    <x-progress x-bind:value="progress" x-bind:max="progressMax" class="h-2"
                        x-bind:class="progressColor" />
                </div>

                {{-- <div x-text="mode"></div>
                <div x-text="currentInterval"></div>
                <div x-text="longInterval"></div> --}}

                <div class="flex flex-col items-center max-w-sm gap-10 py-6 mx-auto">
                    <div class="flex font-mono text-9xl">
                        <span x-text="minutes.toString().padStart(2, '0')"></span>
                        <span>:</span>
                        <span x-text="seconds.toString().padStart(2, '0')"></span>
                    </div>
                    <div class="text-lg text-center capitalize">
                        <template x-if="mode === 'focus'">
                            <span>
                                Stay focus for <span x-text="focusMinutes"></span> minutes
                            </span>
                        </template>
                        <template x-if="mode === 'break'">
                            <span>
                                Take a break for <span x-text="breakMinutes"></span> minutes
                            </span>
                        </template>
                        <template x-if="mode === 'long'">
                            <span>
                                Take a timeout and recharge with fresh ideas.
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
                    <div class="w-full">
                        <div class="flex items-center justify-center w-full gap-2 p-2 text-lg text-center border-2 rounded-lg cursor-pointer border-primary-content bg-primary-content"
                            x-on:click="addTask()" x-show="!activeTasks.length" x-transition>
                            <x-icon name="o-plus-circle" />
                            Add task
                        </div>

                        <div class="flex items-center justify-between w-full p-2 border-2 rounded-lg bg-base-200 border-base-200"
                            x-show="activeTasks.length" x-transition>
                            <div class="text-lg " x-text="activeTasks[0]?.description"></div>
                            <x-button class="btn-primary btn-sm" label="Done"
                                x-on:click="activeTasks[0].isDone=!activeTasks[0].isDone">
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endvolt
</x-layouts.app>
