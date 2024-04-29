<?php

use function Livewire\Volt\{state};

//

?>

<x-layouts.app>
    @volt
        <div x-data="{
            turn: 'X',
            cells: Array(9).fill(''),
            winner: null,
            isDraw: false,
            score: {
                X: 0,
                O: 0,
                draw: 0,
            },
            isRunning: false,
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
            toggleTurn() {
                this.turn = this.turn === 'X' ? 'O' : 'X';
            },
            checkWinner() {
                this.combos.forEach((combo) => {
                    {{-- console.log(this.cells[combo[0]] + ' = ' + this.cells[combo[1]]);
                    console.log(this.cells[combo[1]] + ' = ' + this.cells[combo[2]]);
                    console.log('---'); --}}
                    if (this.cells[combo[0]] !== '' &&
                        this.cells[combo[0]] === this.cells[combo[1]] &&
                        this.cells[combo[1]] === this.cells[combo[2]]
                    ) {
                        this.winner = this.cells[combo[0]];
                        this.score[this.winner]++;
                    }
        
                });
        
                if (!this.winner && !this.cells.includes('')) {
                    this.isDraw = true;
                    this.score['draw']++;
                }
                {{-- console.log('-------------------------------------------------'); --}}
            },
            clickCell(index) {
                if (!this.cells.includes('') || this.winner) {
                    this.resetGame();
                    return;
                }
        
                if (this.cells[index] !== '' || this.winner) {
                    return;
                }
        
        
                this.cells[index] = this.turn;
                this.checkWinner();
                {{-- this.write(index); --}}
                this.toggleTurn();
            },
            resetGame() {
                this.cells = Array(9).fill('');
                this.winner = null;
                this.isDraw = false;
            },
            write(index) {
                let ctx = document.getElementById('canvas-' + index).getContext('2d');
                let data = {
                    dashLen: 220,
                    dashOffset: 220,
                    speed: 5,
                    txt: this.turn,
                    x: 30,
                    i: 0,
                };
        
                ctx.font = '50px Comic Sans MS, cursive, TSCu_Comic, sans-serif';
                ctx.lineWidth = 5;
                ctx.lineJoin = 'round';
                ctx.globalAlpha = 2 / 3;
                ctx.strokeStyle = ctx.fillStyle = '#000000';
        
                this.loop(ctx, data);
            },
            loop(ctx, data) {
                console.log(data.dashOffset);
        
                ctx.clearRect(data.x, 0, 60, 150);
                ctx.setLineDash([data.dashLen - data.dashOffset, data.dashOffset - data.speed]); // create a long dash mask
                data.dashOffset -= data.speed; // reduce dash length
                ctx.strokeText(data.txt[data.i], data.x, 90); // stroke letter
                if (data.dashOffset > 0) {
                    console.log('yes');
                    requestAnimationFrame(this.loop(ctx, data));
                } else {
                    console.log('nope');
                    console.log(data.i);
                    console.log(data.txt.length);
                    ctx.fillText(data.txt[data.i], data.x, 90);
                    data.dashOffset = data.dashLen;
                    data.x += ctx.measureText(data.txt[data.i++]).width + ctx.lineWidth * Math.random();
                    ctx.setTransform(1, 0, 0, 1, 0, 3 * Math.random());
                    ctx.rotate(Math.random() * 0.005);
                    {{-- if (data.i < data.txt.length) {
                        requestAnimationFrame(this.loop(ctx, data));
                    } --}}
                }
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
                <div class="grid grid-cols-3 grid-rows-3">
                    <template x-for="(cell, index) in cells">
                        <div class="flex items-center justify-center border-2 w-28 h-28 lg:w-32 lg:h-32"
                            x-on:click="clickCell(index)"
                            x-bind:class="{
                                'border-t-0': [0, 1, 2].includes(index),
                                'border-r-0': [2, 5, 8].includes(index),
                                'border-b-0': [6, 7, 8].includes(index),
                                'border-l-0': [0, 3, 6].includes(index),
                            }">
                            <div class="transition-all scale-0 text-8xl" x-text="cell"
                                x-bind:class="{ 'scale-100': cell !== '' }">
                            </div>
                            {{-- <canvas class="w-full h-full" x-bind:id="'canvas-' + index">

                            </canvas> --}}
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
                            Player (O)
                        </div>
                        <div class="text-2xl font-bold" x-text="score.O"></div>
                    </div>
                </div>

                {{-- <div>
                    <x-button icon="o-arrow-path" label="Restart" x-on:click="resetGame" />
                </div> --}}
            </div>

            {{-- <canvas width=630></canvas> --}}


        </div>
    @endvolt
</x-layouts.app>
