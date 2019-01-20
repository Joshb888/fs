AddEventHandler('playerConnecting', function(PlayerName, KickReason, Deferrals)
    if not string.find(GetPlayerIdentifiers(source)[1], "steam:") then
		Deferrals.defer()
		Deferrals.done('Steam is required to play on this server, please relaunch your game with steam open.')
    end
end)

Citizen.CreateThread(function()
    while true do
        PerformHttpRequest(Config['Website'] .. '/api.php?endpoint=vehicles', function(statusCode, response, headers)
            if response then
                local Information = json.decode(response)
		if Information.content then
			Information = Information.content
			print('[Hydrid CAD System] Recieved license plates, sending to client now...')
			TriggerClientEvent('RecieveAPIVehicles', -1, Information)
		else
			print('[Hydrid CAD System][Error] No license plates found!')		
		end
            end
        end)
        Citizen.Wait(3 * 60000)
    end
end)

Citizen.CreateThread(function()
    while true do
        PerformHttpRequest(Config['Website'] .. '/api.php?endpoint=vehicles', function(statusCode, response, headers)
            if response then
                local Information = json.decode(response)
			    if Information and Information.content then
					Information = Information.content
					print('[Hydrid CAD System] Recieved license plates, sending to client now...')
					TriggerClientEvent('RecieveAPIVehicles', -1, Information)
				else
					print('[Hydrid CAD System][Error] No license plates found!')
				end
			end
        end)
        Citizen.Wait(3 * 60000)
    end
end)

Citizen.CreateThread(function()
    if Config['AutomaticMessages'] then
        while true do
            PerformHttpRequest(Config['Website'] .. '/api.php?endpoint=identities', function(statusCode, response, headers)
                if response then
                    local Information = json.decode(response)
					if Information and Information.content then
	                    local Amount = 0
	                    Information = Information.content
	                    for a = 1, #Information do
	                        Amount = Amount + 1
	                    end
	                    TriggerClientEvent('chatMessage', -1, '', {255, 255, 255}, Config['ChatPrefix'] .. '^7 We currently have ^4' .. Amount .. '^7 characters registered in our CAD system! ^2' .. Config['Website'])
					else
						print('[Hydrid CAD System][Error] No identities found!')
					end
				end
            end)
    		Citizen.Wait(Config['AutomaticMessagesTime'] * 60000)
        end
    end
end)
