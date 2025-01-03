<?php

use Livewire\Volt\Component;

new #[\Livewire\Attributes\Layout('layouts.app')] class extends Component {
    #[\Livewire\Attributes\Url('playwright', true)]
    public int|null $playwrightId;
    public string|null $playwrightContent = null;

    public \App\Models\Site $site;

    public string $label = '';

    public function setPlaywright($id)
    {
        // just update the URL and reload the page
        $this->playwrightId = $id;
        // refresh the page
        $this->js('window.location.href = window.location.href.split("?")[0] + "?playwright=" + ' . $id);
    }

    public function createPlaywright()
    {
        $playwright = $this->site->playwrights()->create([
            'script' => '',
            'label' => $this->label,
        ]);

        $this->setPlaywright($playwright->id);
    }

    public function savePlaywright()
    {
        $playwright = $this->site->playwrights()->find($this->playwrightId);
        $playwright->update([
            'script' => $this->playwrightContent,
        ]);
    }

    public function deletePlaywright()
    {
        $playwright = $this->site->playwrights()->find($this->playwrightId);
        $playwright->delete();

        $this->setPlaywright(null);


    }

    public function mount(\App\Models\Site $site)
    {
        $this->site = $site;

        $playwrightId = request('playwright');
        if ($playwrightId) {
            $this->playwrightId = $playwrightId;
            $playwright = $this->site->playwrights()->find($playwrightId);
            $this->playwrightContent = $playwright->script;
        } else {
            $this->playwrightContent = "Select a script to edit or create a new one.";
        }
    }
}; ?>

<div class="container mx-auto p-6" x-data="{
    monacoContent: $wire.playwrightContent,
}">

    <div class="grid grid-cols-6">
        <div class="flex flex-col gap-2">
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center"
                    x-on:click.prevent="$dispatch('open-modal', 'site-form')"
                    x-data="{}"
            >
                <i class="fas fa-plus-circle mr-2"></i>
                Add New Script
            </button>
            @foreach($site->playwrights as $playwright)
                <button wire:click="setPlaywright({{ $playwright->id }})" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    {{$playwright->label}}
                </button>

            @endforeach
        </div>

        <div x-data="{
        monacoLanguage: 'javascript',
        monacoPlaceholder: true,
        monacoPlaceholderText: 'Start typing here',
        monacoLoader: true,
        monacoFontSize: '15px',
        monacoId: $id('monaco-editor'),
        monacoEditor(editor){
            editor.onDidChangeModelContent((e) => {
                this.monacoContent = editor.getValue();
                this.updatePlaceholder(editor.getValue());
                $wire.set('playwrightContent', editor.getValue());
            });

            editor.onDidBlurEditorWidget(() => {
                this.updatePlaceholder(editor.getValue());
            });

            editor.onDidFocusEditorWidget(() => {
                this.updatePlaceholder(editor.getValue());
            });
        },
        updatePlaceholder: function(value) {
            if (value == '') {
                this.monacoPlaceholder = true;
                return;
            }
            this.monacoPlaceholder = false;
        },
        monacoEditorFocus(){
            document.getElementById(this.monacoId).dispatchEvent(new CustomEvent('monaco-editor-focused', { monacoId: this.monacoId }));
        },
        monacoEditorAddLoaderScriptToHead() {
            script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs/loader.min.js';
            document.head.appendChild(script);
        }
    }"
             x-init="

        if(typeof _amdLoaderGlobal == 'undefined'){
            monacoEditorAddLoaderScriptToHead();
        }

        monacoLoaderInterval = setInterval(function(){
            if(typeof _amdLoaderGlobal !== 'undefined'){

                // Based on https://jsfiddle.net/developit/bwgkr6uq/ which works without needing service worker. Provided by loader.min.js.
                require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs' }});
                let proxy = URL.createObjectURL(new Blob([` self.MonacoEnvironment = { baseUrl: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min' }; importScripts('https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.39.0/min/vs/base/worker/workerMain.min.js');`], { type: 'text/javascript' }));
                window.MonacoEnvironment = { getWorkerUrl: () => proxy };

                require(['vs/editor/editor.main'], function() {

                    monacoTheme = {'base':'vs-dark','inherit':true,'rules':[{'background':'0C1021','token':''},{'foreground':'aeaeae','token':'comment'},{'foreground':'d8fa3c','token':'constant'},{'foreground':'ff6400','token':'entity'},{'foreground':'fbde2d','token':'keyword'},{'foreground':'fbde2d','token':'storage'},{'foreground':'61ce3c','token':'string'},{'foreground':'61ce3c','token':'meta.verbatim'},{'foreground':'8da6ce','token':'support'},{'foreground':'ab2a1d','fontStyle':'italic','token':'invalid.deprecated'},{'foreground':'f8f8f8','background':'9d1e15','token':'invalid.illegal'},{'foreground':'ff6400','fontStyle':'italic','token':'entity.other.inherited-class'},{'foreground':'ff6400','token':'string constant.other.placeholder'},{'foreground':'becde6','token':'meta.function-call.py'},{'foreground':'7f90aa','token':'meta.tag'},{'foreground':'7f90aa','token':'meta.tag entity'},{'foreground':'ffffff','token':'entity.name.section'},{'foreground':'d5e0f3','token':'keyword.type.variant'},{'foreground':'f8f8f8','token':'source.ocaml keyword.operator.symbol'},{'foreground':'8da6ce','token':'source.ocaml keyword.operator.symbol.infix'},{'foreground':'8da6ce','token':'source.ocaml keyword.operator.symbol.prefix'},{'fontStyle':'underline','token':'source.ocaml keyword.operator.symbol.infix.floating-point'},{'fontStyle':'underline','token':'source.ocaml keyword.operator.symbol.prefix.floating-point'},{'fontStyle':'underline','token':'source.ocaml constant.numeric.floating-point'},{'background':'ffffff08','token':'text.tex.latex meta.function.environment'},{'background':'7a96fa08','token':'text.tex.latex meta.function.environment meta.function.environment'},{'foreground':'fbde2d','token':'text.tex.latex support.function'},{'foreground':'ffffff','token':'source.plist string.unquoted'},{'foreground':'ffffff','token':'source.plist keyword.operator'}],'colors':{'editor.foreground':'#F8F8F8','editor.background':'#0C1021','editor.selectionBackground':'#253B76','editor.lineHighlightBackground':'#FFFFFF0F','editorCursor.foreground':'#FFFFFFA6','editorWhitespace.foreground':'#FFFFFF40'}};
                    monaco.editor.defineTheme('blackboard', monacoTheme);
                    document.getElementById(monacoId).editor = monaco.editor.create($refs.monacoEditorElement, {
                        value: monacoContent,
                        theme: 'blackboard',
                        fontSize: monacoFontSize,
                        lineNumbersMinChars: 3,
                        automaticLayout: true,
                        language: monacoLanguage
                    });
                    monacoEditor(document.getElementById(monacoId).editor);
                    document.getElementById(monacoId).addEventListener('monaco-editor-focused', function(event){
                        document.getElementById(monacoId).editor.focus();
                    });
                    updatePlaceholder(document.getElementById(monacoId).editor.getValue());

                });

                clearInterval(monacoLoaderInterval);
                monacoLoader = false;
            }
        }, 5);
    " :id="monacoId" class="flex flex-col items-center relative justify-start w-full bg-[#0C1021] min-h-[250px] pt-3 h-screen col-span-4 " wire:ignore>
            <div x-show="monacoLoader" class="absolute inset-0 z-20 flex items-center justify-center w-full h-full duration-1000 ease-out">
                <svg class="w-4 h-4 text-gray-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>

            <div x-show="!monacoLoader" class="relative z-10 w-full h-full">
                <div x-ref="monacoEditorElement" class="w-full h-full text-lg"></div>
                <div x-ref="monacoPlaceholderElement" x-show="monacoPlaceholder" @click="monacoEditorFocus()" :style="'font-size: ' + monacoFontSize" class="w-full text-sm font-mono absolute z-50 text-gray-500 ml-14 -translate-x-0.5 mt-0.5 left-0 top-0" x-text="monacoPlaceholderText"></div>
            </div>
        </div>


        <div>

            <x-primary-button wire:click="savePlaywright" class="w-full">
                Save
            </x-primary-button>

            <x-danger-button wire:click="deletePlaywright" class="w-full mt-2">
                Delete
            </x-danger-button>
        </div>
    </div>





    <x-modal name="site-form" :show="$errors->isNotEmpty()" focusable>
        <header class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Create a new script') }}
            </h2>
        </header>

        <form wire:submit.prevent="createPlaywright" class="p-6">
            <div class="mt-6">
                <x-input-label for="label" value="{{ __('Label') }}" class="sr-only" />
                <x-text-input
                    wire:model="label"
                    id="label"
                    name="label"
                    type="text"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Label') }}"
                />
                <x-input-error :messages="$errors->get('label')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-primary-button>
                    {{ __('Create') }}
                </x-primary-button>

                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>
            </div>
        </form>
    </x-modal>
</div>
