<?php

use function Livewire\Volt\{state};

//

?>

<x-layouts.app>
    @volt
        <div x-data="{
            turn: 'X',
            firstTurn: 'X',
            cells: Array(9).fill(''),
            isAnimating: Array(9).fill(false),
            isBot: true,
            difficulties: ['Easy', 'PvP'],
            selectedDifficulty: 'Easy',
            winner: null,
            isDraw: false,
            score: {
                X: 0,
                O: 0,
                draw: 0,
            },
            combos: [
                [0, 1, 2],
                [3, 4, 5],
                [6, 7, 8],
                [0, 3, 6],
                [1, 4, 7],
                [2, 5, 8],
                [0, 4, 8],
                [2, 4, 6],
            ],
            resetGame() {
                this.cells = Array(9).fill('');
                this.winner = null;
                this.isDraw = false;
                this.startBot();
            },
            resetScore() {
                this.score = {
                    X: 0,
                    O: 0,
                    draw: 0,
                }
            },
            startBot() {
                if (this.isBot && this.turn === 'O') {
                    this.botMove();
                }
            },
            setBot(value) {
                this.isBot = !(this.selectedDifficulty !== 'PvP');
                this.resetGame();
                this.resetScore();
            },
            humanMove(index) {
                if (!this.cells.includes('') || this.winner) {
                    this.resetGame();
                    return;
                }
        
                this.move(index);
                this.startBot();
            },
            botMove() {
                let availableCells = this.cells.reduce((acc, cell, index) => {
                    if (cell === '') {
                        acc.push(index);
                    }
                    return acc;
                }, []);
        
                let randomIndex = Math.floor(Math.random() * availableCells.length);
                let botMoveIndex = availableCells[randomIndex];
        
                setTimeout(() => {
                    this.move(botMoveIndex);
                }, 300);
            },
            move(index) {
                if (this.cells[index] !== '' || this.winner) {
                    return;
                }
        
                this.animateCell(index);
                this.setCell(index);
                this.checkWinner();
                this.toggleTurn();
                this.toggleFirstTurn();
            },
            toggleTurn() {
                this.turn = this.turn === 'X' ? 'O' : 'X';
            },
            toggleFirstTurn() {
                if (this.winner && this.isDraw) {
                    this.firstTurn = this.firstTurn === 'X' ? 'O' : 'X';
                    this.turn = this.firstTurn;
                }
            },
            checkWinner() {
                for (const combo of this.combos) {
                    const [a, b, c] = combo;
                    if (this.cells[a] !== '' &&
                        this.cells[a] === this.cells[b] &&
                        this.cells[b] === this.cells[c]
                    ) {
                        this.winner = this.cells[a];
                        this.score[this.winner]++;
                        break;
                    }
                }
        
                if (!this.winner && !this.cells.includes('')) {
                    this.isDraw = true;
                    this.score['draw']++;
                }
            },
            setCell(index) {
                this.cells[index] = this.turn;
            },
            animateCell(index) {
                this.isAnimating[index] = true;
                setTimeout(() => this.isAnimating[index] = false, 200);
            },
        }">
            <x-header title="Tic Tac Toe" size="text-3xl text-primary">
                <x-slot:actions>
                    <x-theme-toggle class="btn" title="Toggle Theme" />
                    <x-button label="" class="" x-on:click="$wire.drawerSettings = true" responsive
                        icon="o-adjustments-horizontal" title="Settings" />
                </x-slot:actions>
            </x-header>

            <div class="m-auto w-full h-[calc(100vh-8rem)] justify-center items-center flex flex-col gap-8 px-4">
                {{-- <div class="text-2xl">Winner: <span x-text="winner"></span></div> --}}
                <select class="max-w-xs max-24 select select-bordered select-sm" x-model="selectedDifficulty"
                    x-on:input="setBot($event.target.value)">
                    <template x-for="difficulty in difficulties">
                        <option x-text="difficulty" x-bind:selected="difficulty === selectedDifficulty"></option>
                    </template>
                </select>
                <div class="grid grid-cols-3 grid-rows-3">
                    <template x-for="(cell, index) in cells">
                        <div class="flex items-center justify-center border-2 w-28 h-28 lg:w-32 lg:h-32"
                            x-on:click="humanMove(index)"
                            x-bind:class="{
                                'border-t-0': [0, 1, 2].includes(index),
                                'border-r-0': [2, 5, 8].includes(index),
                                'border-b-0': [6, 7, 8].includes(index),
                                'border-l-0': [0, 3, 6].includes(index),
                            }">
                            <div class="text-8xl" x-bind:class="{ 'animate-popout': isAnimating[index] }" x-text="cell">
                            </div>
                        </div>
                    </template>
                </div>

                <div class="grid w-full grid-cols-3 gap-4">
                    <div class="py-2 text-center transition-all border-2 rounded"
                        x-bind:class="{
                            'bg-info border-info': !winner && !isDraw && turn === 'X',
                            'bg-success border-success': winner === 'X',
                        }">
                        <div>
                            Player (X)
                        </div>
                        <div class="text-2xl font-bold" x-text="score.X"></div>
                    </div>
                    <div class="py-2 text-center transition-all border-2 rounded"
                        x-bind:class="{
                            'bg-warning border-warning': isDraw,
                        }">
                        <div>
                            Draw
                        </div>
                        <div class="text-2xl font-bold" x-text="score.draw"></div>
                    </div>
                    <div class="py-2 text-center transition-all border-2 rounded"
                        x-bind:class="{
                            'bg-info border-info': !winner && !isDraw && turn === 'O',
                            'bg-success border-success': winner === 'O',
                        }">
                        <div>
                            <template x-if="isBot">
                                <span>Bot</span>
                            </template>
                            <template x-if="!isBot">
                                <span>Player</span>
                            </template>
                            (O)
                        </div>
                        <div class="text-2xl font-bold" x-text="score.O"></div>
                    </div>
                </div>

                {{-- <div>
                    <x-button icon="o-arrow-path" label="Restart" x-on:click="resetGame" />
                </div> --}}
            </div>
        </div>
    @endvolt
</x-layouts.app>
