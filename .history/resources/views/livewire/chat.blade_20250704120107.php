<div
>

    <div  style="
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            background-color: transparent !important;
        ">
        <div style="
                display: flex !important;
                height: 550px !important;
                font-size: 0.875rem !important;             /* text-sm */
                border: 1px solid transparent !important;       /* default border (gray-200) */
                border-radius: 0.75rem !important;          /* rounded-xl */
                box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important; /* default shadow */
                overflow: hidden !important;
                background-color: transparent !important;       /* bg-green-200 */">
                    <div  style="
                    width: 25% !important;
                    border-right: 1px solid #e5e7eb !important; /* Tailwind default border color (gray-200) */
                    background-color: #134b08 !important;       /* bg-green-200 */
                    display: flex !important;
                    flex-direction: column !important;">
                <div
                        style="
                        padding: 1rem !important;                     /* p-4 */
                        font-weight: 700 !important;                  /* font-bold */
                        color: white !important;                    /* text-gray-700 */
                        border-bottom: 1px solid !important;  /* border-b (gray-200) */
                        text-align: center !important;                /* text-center */
                        font-size: 1.125rem !important;               /* text-lg */
                    "
            >Chats</div>
                <div class="divide-y flex-1 overflow-y-auto">
                @if($users->count() > 0)
                    @foreach($users as $user)
                    <div wire:click='selectUser({{ $user->id }})' class="p-3 cursor-pointer hover:bg-blue-100 dark:hover:bg-gray-700 transition 
                        {{ $selectedUser->id === $user->id ? 'bg-blue-200 dark:bg-gray-700' : '' }}">
                    <div class="flex items-center justify-center text-gray-800 dark:text-gray-200">
                        <div style=":hover: background-color: #f0f0f0;" class="flex items-center gap-3 justify-start w-full">
                            <span class="inline-flex items-center justify-center rounded-full border-2 border-[#84c1dd] bg-white dark:bg-gray-900 shadow" style="aspect-ratio:1/1; width:2.25rem; min-width:2.25rem; max-width:100%;">
                               <svg class="w-full h-full p-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                   <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                               </svg>
                           </span>
                           <div>
                               <div class="text-gray-800 dark:text-gray-200">{{$user->name ?? ''}}</div>
                               <div class="text-xs text-gray-500 dark:text-gray-400">{{$user->email ?? ''}}</div>
                           </div>
                       </div>
                       
                   </div>
                   <div class="w-full flex justify-center">
                       @php
                       $roleStyles = [
                           'customer' => 'bg-green-100 text-green-500 dark:bg-green-900 dark:text-green-300',
                           'manufacturer' => 'bg-red-500 text-white dark:bg-red-700 dark:text-white',
                           'vendor' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                           'supplier' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                       ];
                   @endphp
                       <span class="text-xs font-semibold px-2 py-0.5 rounded-full shadow-sm
                           {{ $roleStyles[$user->role] ?? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' }}">
                           {{ ucfirst($user->role) }}
                       </span>
                   </div>
                   </div>
                   @endforeach
                @else
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        No users found. Start a conversation!
                    </div>
                @endif
                </div>
            </div>
            <div class="flex-1 flex flex-col">
                <!-- Header -->
                <div style="background-color: #134b08" class="p-4 border-b bg-gray-50 dark:bg-gray-800 flex items-center gap-4">
                    <span class="inline-flex items-center justify-center rounded-full border-2 border-[#84c1dd] bg-white dark:bg-gray-900 shadow shrink-0" style="aspect-ratio:1/1; width:3.5rem; min-width:3.5rem; max-width:100%;">
                            <svg class="w-full h-full p-2 text-gray-400 dark:text-gray-500 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                       
                    </span>
                    <div >
                        <div >{{$selectedUser->name ?? 'No user'}}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{$selectedUser->email ?? ''}}</div>
                    </div>
                </div>

                <!-- Messages -->
    <div class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto">
        @if(count($messages) === 0)
            <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400 text-base font-medium">
                No messages yet. Start the conversation!
            </div>
        @else
            <div class="p-4 space-y-2">
                
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div 
                            @if($message->sender_id === Auth::id())
                            class="max-w-xs px-4 py-2 rounded-2xl shadow"
                            style="background-color: steelblue !important; color: white !important; border-radius: 5px !important; padding: 0.5rem 1rem !important; font-size: 0.875rem !important; font-weight: 600 !important;"
                            @else
                            style="background-color: #1a7a06 !important; color: white !important; border-radius: 5px !important; padding: 0.5rem 1rem !important; font-size: 0.875rem !important; font-weight: 600 !important;"
                                class="max-w-xs px-4 py-2 rounded-2xl shadow bg-blue-600 text-white dark:bg-blue-700"
                            @endif
                        >
                            {{ $message->message }}
                            <span class="text-gray-400 dark:text-gray-300 text-xs block mt-1">
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                            @if($message->sender_id === Auth::id())
                                <div class="text-green-500 dark:text-green-300 text-xs mt-1 rounded-2xl flex items-center gap-1">
                                    (You)
                                    <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                    <svg class="w-3 h-3 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none"
                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="m4.5 12.75 6 6 9-13.5"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>


                <!-- Input -->
                <form wire:submit="submit" class="p-4 border-t bg-white dark:bg-gray-800 flex items-center gap-2">
                @csrf
                    <input
                        type="text"
                        wire:model='newMessage'
                        class="flex-1 border border-gray-300 dark:border-gray-700 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-300 dark:focus:ring-blue-900 text-black dark:text-white bg-white dark:bg-gray-900"
                        placeholder="Type your message..." />
                    <button type="submit"
                    style="background-color: #134b08 !important; cursor: pointer !important; color: white !important; border-radius: 2rem !important; padding: 0.5rem 1rem !important; font-size: 0.875rem !important; font-weight: 600 !important;"
                        class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-sm px-4 py-2 rounded-full transition">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
